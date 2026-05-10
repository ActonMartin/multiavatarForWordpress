<?php
/**
 * Plugin Name: Multiavatar for WordPress
 * Plugin URI: https://github.com/multiavatar/multiavatar-wordpress
 * Description: 将 Multiavatar 集成到 WordPress，生成独特的 SVG 头像。支持短代码、用户头像替换等功能。
 * Version: 1.0.1
 * Author: Multiavatar
 * Author URI: https://nanhu.pp.ua
 * License: MIT
 * Text Domain: multiavatar-wordpress
 * Domain Path: /languages
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

// 定义插件常量
define('MAWP_VERSION', '1.0.1');
define('MAWP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MAWP_PLUGIN_URL', plugin_dir_url(__FILE__));

// 加载官方 Multiavatar 库
require_once MAWP_PLUGIN_DIR . 'Multiavatar.php';

/**
 * 插件主类
 */
class Multiavatar_WordPress {
    
    /**
     * 单例实例
     */
    private static $instance = null;
    
    /**
     * 插件设置
     */
    private $settings;
    
    /**
     * 获取单例实例
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * 构造函数
     */
    private function __construct() {
        $this->load_settings();
        $this->init_hooks();
    }
    
    /**
     * 加载设置
     */
    private function load_settings() {
        $default_settings = array(
            'replace_avatars' => true,
            'default_size' => 80,
            'enable_shortcode' => true,
            'cache_avatars' => true,
        );
        
        $this->settings = get_option('multiavatar_wp_settings', $default_settings);
    }
    
    /**
     * 初始化钩子
     */
    private function init_hooks() {
        // 加载文本域
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // 注册短代码
        if ($this->settings['enable_shortcode']) {
            add_shortcode('multiavatar', array($this, 'render_shortcode'));
        }
        
        // 替换用户头像
        if ($this->settings['replace_avatars']) {
            add_filter('get_avatar', array($this, 'replace_avatar'), 10, 5);
            add_filter('get_avatar_url', array($this, 'replace_avatar_url'), 10, 3);
        }
        
        // 注册管理菜单
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // 注册设置
        add_action('admin_init', array($this, 'register_settings'));
        
        // 前端脚本
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // AJAX 处理
        add_action('wp_ajax_multiavatar_generate', array($this, 'ajax_generate_avatar'));
        add_action('wp_ajax_nopriv_multiavatar_generate', array($this, 'ajax_generate_avatar'));
    }
    
    /**
     * 加载文本域
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'multiavatar-wordpress',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
    
    /**
     * 注册短代码
     * 
     * 用法: [multiavatar text="用户名" size="80"]
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'text' => '',
            'size' => $this->settings['default_size'],
            'class' => 'multiavatar',
            'id' => '',
        ), $atts, 'multiavatar');
        
        // 如果没有提供文本，使用当前用户
        if (empty($atts['text'])) {
            $atts['text'] = $this->get_user_identifier();
        }
        
        return $this->generate_avatar_html($atts['text'], $atts['size'], $atts['class'], $atts['id']);
    }
    
    /**
     * 替换 WordPress 头像
     */
    public function replace_avatar($avatar, $id_or_email, $size, $default, $alt) {
        $user_identifier = $this->get_user_identifier_from_id($id_or_email);
        
        if (!$user_identifier) {
            return $avatar;
        }
        
        $size = $size ? $size : $this->settings['default_size'];
        
        return $this->generate_avatar_html($user_identifier, $size, 'avatar', '', $alt);
    }
    
    /**
     * 替换头像 URL
     */
    public function replace_avatar_url($url, $id_or_email, $args) {
        $user_identifier = $this->get_user_identifier_from_id($id_or_email);
        
        if (!$user_identifier) {
            return $url;
        }
        
        // 返回 data URL
        $svg = $this->generate_avatar_svg($user_identifier);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
    
    /**
     * 从 ID 或邮箱获取用户标识符
     */
    private function get_user_identifier_from_id($id_or_email) {
        $user_id = false;
        
        if (is_numeric($id_or_email)) {
            $user_id = (int) $id_or_email;
        } elseif (is_object($id_or_email)) {
            // 处理 WP_User, WP_Post, WP_Comment 等对象
            if (isset($id_or_email->user_id) && $id_or_email->user_id) {
                $user_id = $id_or_email->user_id;
            } elseif (isset($id_or_email->ID) && $id_or_email->ID) {
                $user_id = $id_or_email->ID;
            } elseif (isset($id_or_email->comment_author_email) && $id_or_email->comment_author_email) {
                // 对于评论对象，使用评论作者邮箱
                return $id_or_email->comment_author_email;
            }
        } elseif (is_string($id_or_email) && is_email($id_or_email)) {
            $user = get_user_by('email', $id_or_email);
            if ($user) {
                $user_id = $user->ID;
            }
        }
        
        if ($user_id) {
            $user = get_user_by('ID', $user_id);
            if ($user) {
                // 优先使用用户名，其次邮箱
                return $user->user_login ?: $user->user_email;
            }
        }
        
        // 如果是邮箱字符串，直接使用
        if (is_string($id_or_email) && is_email($id_or_email)) {
            return $id_or_email;
        }
        
        return false;
    }
    
    /**
     * 获取当前用户标识符
     */
    private function get_user_identifier() {
        $current_user = wp_get_current_user();
        
        if ($current_user->ID) {
            return $current_user->user_login ?: $current_user->user_email;
        }
        
        // 对于未登录用户，使用 IP 地址或随机字符串
        $ip = $this->get_user_ip();
        return $ip ?: 'guest_' . wp_generate_password(8, false);
    }
    
    /**
     * 获取用户 IP
     */
    private function get_user_ip() {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
        }
        
        return $ip;
    }
    
    /**
     * 生成头像 HTML
     */
    private function generate_avatar_html($text, $size = 80, $class = 'multiavatar', $id = '', $alt = '') {
        $svg = $this->generate_avatar_svg($text);
        
        $id_attr = $id ? ' id="' . esc_attr($id) . '"' : '';
        $alt_attr = $alt ? ' alt="' . esc_attr($alt) . '"' : '';
        
        return sprintf(
            '<img src="data:image/svg+xml;base64,%s" class="%s" width="%d" height="%d"%s%s />',
            base64_encode($svg),
            esc_attr($class),
            intval($size),
            intval($size),
            $id_attr,
            $alt_attr
        );
    }
    
    /**
     * 生成 SVG 头像（使用官方 Multiavatar 库）
     */
    public function generate_avatar_svg($text) {
        // 使用缓存
        if ($this->settings['cache_avatars']) {
            $cache_key = 'multiavatar_' . md5($text);
            $cached = get_transient($cache_key);
            
            // 验证缓存的是有效的 SVG（检查是否包含 svg 标签）
            if ($cached && strpos($cached, '<svg') !== false && strpos($cached, '</svg>') !== false) {
                return $cached;
            }
        }
        
        // 使用官方 Multiavatar 库生成 SVG
        $multiavatar = new Multiavatar();
        $svg = $multiavatar($text, null, null);
        
        // 缓存结果
        if ($this->settings['cache_avatars']) {
            set_transient($cache_key, $svg, DAY_IN_SECONDS * 30);
        }
        
        return $svg;
    }
    
    /**
     * 添加管理菜单
     */
    public function add_admin_menu() {
        // 添加到设置菜单
        add_options_page(
            __('Multiavatar 设置', 'multiavatar-wordpress'),
            __('Multiavatar', 'multiavatar-wordpress'),
            'manage_options',
            'multiavatar-settings',
            array($this, 'render_settings_page')
        );
        
        // 同时添加顶级菜单（更容易找到）
        add_menu_page(
            __('Multiavatar 设置', 'multiavatar-wordpress'),
            'Multiavatar',
            'manage_options',
            'multiavatar-settings',
            array($this, 'render_settings_page'),
            'dashicons-admin-users',
            30
        );
    }
    
    /**
     * 注册设置
     */
    public function register_settings() {
        register_setting('multiavatar_wp_settings_group', 'multiavatar_wp_settings', array(
            'sanitize_callback' => array($this, 'sanitize_settings'),
        ));
        
        add_settings_section(
            'multiavatar_main_section',
            __('主要设置', 'multiavatar-wordpress'),
            null,
            'multiavatar-settings'
        );
        
        add_settings_field(
            'replace_avatars',
            __('替换用户头像', 'multiavatar-wordpress'),
            array($this, 'render_field_replace_avatars'),
            'multiavatar-settings',
            'multiavatar_main_section'
        );
        
        add_settings_field(
            'default_size',
            __('默认头像大小', 'multiavatar-wordpress'),
            array($this, 'render_field_default_size'),
            'multiavatar-settings',
            'multiavatar_main_section'
        );
        
        add_settings_field(
            'enable_shortcode',
            __('启用短代码', 'multiavatar-wordpress'),
            array($this, 'render_field_enable_shortcode'),
            'multiavatar-settings',
            'multiavatar_main_section'
        );
        
        add_settings_field(
            'cache_avatars',
            __('缓存头像', 'multiavatar-wordpress'),
            array($this, 'render_field_cache_avatars'),
            'multiavatar-settings',
            'multiavatar_main_section'
        );
    }
    
    /**
     * 清理设置
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        
        $sanitized['replace_avatars'] = isset($input['replace_avatars']) ? (bool) $input['replace_avatars'] : false;
        $sanitized['default_size'] = isset($input['default_size']) ? absint($input['default_size']) : 80;
        $sanitized['enable_shortcode'] = isset($input['enable_shortcode']) ? (bool) $input['enable_shortcode'] : false;
        $sanitized['cache_avatars'] = isset($input['cache_avatars']) ? (bool) $input['cache_avatars'] : false;
        
        return $sanitized;
    }
    
    /**
     * 渲染字段：替换头像
     */
    public function render_field_replace_avatars() {
        $checked = checked($this->settings['replace_avatars'], true, false);
        echo '<input type="checkbox" name="multiavatar_wp_settings[replace_avatars]" value="1" ' . $checked . ' />';
        echo '<p class="description">' . esc_html__('使用 Multiavatar 替换 WordPress 默认头像', 'multiavatar-wordpress') . '</p>';
    }
    
    /**
     * 渲染字段：默认大小
     */
    public function render_field_default_size() {
        $value = esc_attr($this->settings['default_size']);
        echo '<input type="number" name="multiavatar_wp_settings[default_size]" value="' . $value . '" min="16" max="512" />';
        echo ' <span>px</span>';
    }
    
    /**
     * 渲染字段：启用短代码
     */
    public function render_field_enable_shortcode() {
        $checked = checked($this->settings['enable_shortcode'], true, false);
        echo '<input type="checkbox" name="multiavatar_wp_settings[enable_shortcode]" value="1" ' . $checked . ' />';
        echo '<p class="description">' . esc_html__('启用 [multiavatar] 短代码', 'multiavatar-wordpress') . '</p>';
    }
    
    /**
     * 渲染字段：缓存头像
     */
    public function render_field_cache_avatars() {
        $checked = checked($this->settings['cache_avatars'], true, false);
        echo '<input type="checkbox" name="multiavatar_wp_settings[cache_avatars]" value="1" ' . $checked . ' />';
        echo '<p class="description">' . esc_html__('缓存生成的头像以提高性能', 'multiavatar-wordpress') . '</p>';
    }
    
    /**
     * 渲染设置页面
     */
    public function render_settings_page() {
        // 处理清除缓存
        if (isset($_POST['clear_cache']) && check_admin_referer('multiavatar_clear_cache')) {
            global $wpdb;
            $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_multiavatar_%'");
            $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_multiavatar_%'");
            echo '<div class="notice notice-success is-dismissible"><p>✅ 头像缓存已清除！请刷新页面查看新头像。</p></div>';
        }
        
        // 获取当前用户信息
        $current_user = wp_get_current_user();
        $user_identifier = $current_user->ID ? ($current_user->user_login ?: $current_user->user_email) : 'guest';
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <!-- 清除缓存按钮 -->
            <form method="post" action="" style="margin-bottom: 20px;">
                <?php wp_nonce_field('multiavatar_clear_cache'); ?>
                <input type="hidden" name="clear_cache" value="1">
                <button type="submit" class="button button-secondary" onclick="return confirm('确定要清除所有头像缓存吗？');">
                    🗑️ 清除头像缓存
                </button>
                <span style="margin-left: 10px; color: #666; font-size: 12px;">
                    如果头像没有更新，点击此按钮清除缓存
                </span>
            </form>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('multiavatar_wp_settings_group');
                do_settings_sections('multiavatar-settings');
                submit_button();
                ?>
            </form>
            
            <hr>
            
            <!-- 头像生成原理说明 -->
            <h2>🎯 头像生成原理</h2>
            <div class="notice notice-info inline" style="padding: 10px;">
                <h3 style="margin-top: 0;">计算依据</h3>
                <p><strong>Multiavatar 基于「文本字符串」生成头像，采用确定性算法：</strong></p>
                <ol>
                    <li><strong>输入文本</strong> → 可以是用户名、邮箱、IP 地址等任意字符串</li>
                    <li><strong>SHA256 哈希</strong> → 使用 SHA256 算法生成哈希值</li>
                    <li><strong>提取数字</strong> → 从哈希中只保留数字字符</li>
                    <li><strong>取前 12 位</strong> → 用于计算 6 个头像部分</li>
                    <li><strong>计算各部分</strong> → 每部分独立选择设计(00-47)和主题(A/B/C)</li>
                    <li><strong>生成 SVG</strong> → 组合 6 个部分生成最终头像</li>
                </ol>
                <p style="color: #0073aa;"><strong>✨ 核心特性：相同文本 = 相同头像（确定性算法）</strong></p>
                <p style="color: #d63638;"><strong>⚠️ 注意：使用 SHA256 算法，不是 MD5！</strong></p>
            </div>
            
            <!-- 当前用户头像信息 -->
            <h2>👤 当前用户头像信息</h2>
            <?php
            // 计算官方 Multiavatar 的哈希值
            $sha256_hash = hash('sha256', $user_identifier);
            $sha256_numbers = preg_replace("/[^0-9]/", "", $sha256_hash);
            $hash_12 = substr($sha256_numbers, 0, 12);
            
            // 计算各部分的设计和主题
            function calculate_part($hash, $pos1, $pos2) {
                $value = $hash[$pos1] . $hash[$pos2];
                $nr = round((47/100) * $value);
                
                if ($nr > 31) {
                    $nr = $nr - 32;
                    return sprintf('%02dC', $nr);
                } else if ($nr > 15) {
                    $nr = $nr - 16;
                    return sprintf('%02dB', $nr);
                } else {
                    return sprintf('%02dA', $nr);
                }
            }
            
            $parts = array(
                'env' => calculate_part($hash_12, 0, 1),
                'clo' => calculate_part($hash_12, 2, 3),
                'head' => calculate_part($hash_12, 4, 5),
                'mouth' => calculate_part($hash_12, 6, 7),
                'eyes' => calculate_part($hash_12, 8, 9),
                'top' => calculate_part($hash_12, 10, 11)
            );
            ?>
            <table class="widefat" style="max-width: 800px;">
                <tbody>
                    <tr>
                        <th width="30%">用户 ID</th>
                        <td><?php echo $current_user->ID ?: '未登录'; ?></td>
                    </tr>
                    <tr>
                        <th>用户名</th>
                        <td><?php echo $current_user->user_login ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <th>邮箱</th>
                        <td><?php echo $current_user->user_email ?: '-'; ?></td>
                    </tr>
                    <tr>
                        <th><strong style="color: #0073aa;">头像计算依据</strong></th>
                        <td><strong style="color: #0073aa; font-size: 14px;"><?php echo esc_html($user_identifier); ?></strong></td>
                    </tr>
                    <tr>
                        <th>SHA256 哈希</th>
                        <td><code style="font-size: 11px; word-break: break-all;"><?php echo $sha256_hash; ?></code></td>
                    </tr>
                    <tr>
                        <th>提取数字（前12位）</th>
                        <td><code style="font-size: 13px; color: #0073aa; font-weight: bold;"><?php echo $hash_12; ?></code></td>
                    </tr>
                    <tr>
                        <th>环境 (env)</th>
                        <td>设计 <?php echo substr($parts['env'], 0, 2); ?> / 主题 <?php echo substr($parts['env'], 2, 1); ?></td>
                    </tr>
                    <tr>
                        <th>衣服 (clo)</th>
                        <td>设计 <?php echo substr($parts['clo'], 0, 2); ?> / 主题 <?php echo substr($parts['clo'], 2, 1); ?></td>
                    </tr>
                    <tr>
                        <th>头部 (head)</th>
                        <td>设计 <?php echo substr($parts['head'], 0, 2); ?> / 主题 <?php echo substr($parts['head'], 2, 1); ?></td>
                    </tr>
                    <tr>
                        <th>嘴巴 (mouth)</th>
                        <td>设计 <?php echo substr($parts['mouth'], 0, 2); ?> / 主题 <?php echo substr($parts['mouth'], 2, 1); ?></td>
                    </tr>
                    <tr>
                        <th>眼睛 (eyes)</th>
                        <td>设计 <?php echo substr($parts['eyes'], 0, 2); ?> / 主题 <?php echo substr($parts['eyes'], 2, 1); ?></td>
                    </tr>
                    <tr>
                        <th>顶部 (top)</th>
                        <td>设计 <?php echo substr($parts['top'], 0, 2); ?> / 主题 <?php echo substr($parts['top'], 2, 1); ?></td>
                    </tr>
                    <tr>
                        <th>头像预览</th>
                        <td><?php echo $this->render_shortcode(array('text' => $user_identifier, 'size' => 80)); ?></td>
                    </tr>
                </tbody>
            </table>
            
            <hr>
            
            <!-- 不同场景的计算依据 -->
            <h2>📋 不同场景的计算依据</h2>
            <table class="widefat" style="max-width: 800px;">
                <thead>
                    <tr>
                        <th width="25%">场景</th>
                        <th width="35%">计算依据</th>
                        <th width="40%">说明</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>已登录用户</strong></td>
                        <td>用户名 或 邮箱</td>
                        <td>优先使用用户名，其次邮箱</td>
                    </tr>
                    <tr>
                        <td><strong>评论者（有邮箱）</strong></td>
                        <td>评论者邮箱</td>
                        <td>使用评论时填写的邮箱</td>
                    </tr>
                    <tr>
                        <td><strong>评论者（无邮箱）</strong></td>
                        <td>IP 地址</td>
                        <td>使用评论者的 IP 地址</td>
                    </tr>
                    <tr>
                        <td><strong>短代码指定</strong></td>
                        <td>text 参数值</td>
                        <td>如 [multiavatar text="john"]</td>
                    </tr>
                    <tr>
                        <td><strong>未登录访客</strong></td>
                        <td>IP 地址 或 随机字符串</td>
                        <td>优先 IP，无法获取则随机</td>
                    </tr>
                </tbody>
            </table>
            
            <hr>
            
            <h2><?php esc_html_e('使用说明', 'multiavatar-wordpress'); ?></h2>
            
            <h3><?php esc_html_e('短代码使用', 'multiavatar-wordpress'); ?></h3>
            <p><?php esc_html_e('在文章或页面中使用以下短代码：', 'multiavatar-wordpress'); ?></p>
            <code>[multiavatar text="用户名" size="80"]</code>
            
            <h3><?php esc_html_e('参数说明', 'multiavatar-wordpress'); ?></h3>
            <ul>
                <li><strong>text</strong>: <?php esc_html_e('用于生成头像的文本（用户名、邮箱等）', 'multiavatar-wordpress'); ?></li>
                <li><strong>size</strong>: <?php esc_html_e('头像大小（像素）', 'multiavatar-wordpress'); ?></li>
                <li><strong>class</strong>: <?php esc_html_e('CSS 类名', 'multiavatar-wordpress'); ?></li>
                <li><strong>id</strong>: <?php esc_html_e('元素 ID', 'multiavatar-wordpress'); ?></li>
            </ul>
            
            <h3><?php esc_html_e('示例', 'multiavatar-wordpress'); ?></h3>
            <ul>
                <li><code>[multiavatar]</code> - <?php esc_html_e('显示当前用户的头像', 'multiavatar-wordpress'); ?></li>
                <li><code>[multiavatar text="john"]</code> - <?php esc_html_e('显示用户名为 "john" 的头像', 'multiavatar-wordpress'); ?></li>
                <li><code>[multiavatar text="user@example.com" size="120"]</code> - <?php esc_html_e('显示邮箱对应的头像，大小为 120px', 'multiavatar-wordpress'); ?></li>
            </ul>
        </div>
        <?php
    }
    
    /**
     * 加载前端脚本
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'multiavatar-js',
            MAWP_PLUGIN_URL . 'assets/multiavatar.min.js',
            array(),
            MAWP_VERSION,
            true
        );
        
        wp_enqueue_script(
            'multiavatar-wp-frontend',
            MAWP_PLUGIN_URL . 'assets/frontend.js',
            array('multiavatar-js'),
            MAWP_VERSION,
            true
        );
        
        wp_localize_script('multiavatar-wp-frontend', 'multiavatarWp', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('multiavatar_nonce'),
        ));
    }
    
    /**
     * AJAX 生成头像
     */
    public function ajax_generate_avatar() {
        check_ajax_referer('multiavatar_nonce', 'nonce');
        
        $text = isset($_POST['text']) ? sanitize_text_field($_POST['text']) : '';
        $size = isset($_POST['size']) ? absint($_POST['size']) : 80;
        
        if (empty($text)) {
            wp_send_json_error(array('message' => 'Text parameter is required'));
        }
        
        $html = $this->generate_avatar_html($text, $size);
        
        wp_send_json_success(array('html' => $html));
    }
}

// 初始化插件
function multiavatar_wp_init() {
    return Multiavatar_WordPress::get_instance();
}

// 启动插件
add_action('plugins_loaded', 'multiavatar_wp_init');
