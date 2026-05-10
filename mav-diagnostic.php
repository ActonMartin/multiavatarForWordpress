<?php
/**
 * Plugin Name: Multiavatar 诊断工具
 * Description: 诊断 Multiavatar 生成问题
 * Version: 1.0
 */

add_action('admin_menu', function() {
    add_menu_page('MAV 诊断', 'MAV 诊断', 'manage_options', 'mav-diagnostic', 'mav_diagnostic_page', 'dashicons-warning', 99);
});

function mav_diagnostic_page() {
    // 加载 Multiavatar 库 - 尝试多个可能的路径
    $possible_paths = array(
        // 路径1: 同目录下
        plugin_dir_path(__FILE__) . 'Multiavatar.php',
        // 路径2: 主插件目录
        WP_PLUGIN_DIR . '/multiavatar-wordpress/Multiavatar.php',
        // 路径3: 当前插件的上级目录
        dirname(plugin_dir_path(__FILE__)) . '/multiavatar-wordpress/Multiavatar.php',
        // 路径4: 如果诊断工具在主插件目录中
        dirname(__FILE__) . '/Multiavatar.php',
    );
    
    $lib_path = null;
    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            $lib_path = $path;
            break;
        }
    }
    
    if (!$lib_path) {
        ?>
        <div class="wrap">
            <h1>❌ 错误：找不到 Multiavatar.php</h1>
            <div class="notice notice-error">
                <p><strong>尝试的路径：</strong></p>
                <ul>
                    <?php foreach ($possible_paths as $path): ?>
                        <li><code><?php echo esc_html($path); ?></code></li>
                    <?php endforeach; ?>
                </ul>
                <p><strong>解决方法：</strong></p>
                <ol>
                    <li>确保 Multiavatar.php 文件存在</li>
                    <li>检查文件权限</li>
                    <li>确认插件目录结构正确</li>
                </ol>
                <p><strong>正确的目录结构：</strong></p>
                <pre>/wp-content/plugins/multiavatar-wordpress/
├── multiavatar-wordpress.php
├── Multiavatar.php          ← 这个文件必须存在
├── mav-diagnostic.php       ← 诊断工具
└── assets/</pre>
            </div>
        </div>
        <?php
        return;
    }
    
    require_once $lib_path;
    
    $test_text = isset($_GET['text']) ? sanitize_text_field($_GET['text']) : 'actonmartin';
    
    // 生成头像
    $multiavatar = new Multiavatar();
    $svg = $multiavatar($test_text, null, null);
    
    // 计算过程
    $sha256 = hash('sha256', $test_text);
    $numbers = preg_replace("/[^0-9]/", "", $sha256);
    $hash_12 = substr($numbers, 0, 12);
    
    ?>
    <div class="wrap">
        <h1>🔍 Multiavatar 诊断工具</h1>
        
        <form method="get" style="margin: 20px 0;">
            <input type="hidden" name="page" value="mav-diagnostic">
            <label>测试文本：</label>
            <input type="text" name="text" value="<?php echo esc_attr($test_text); ?>" style="width: 300px;">
            <button type="submit" class="button button-primary">测试</button>
        </form>
        
        <hr>
        
        <!-- 对比显示 -->
        <div style="display: flex; gap: 30px; margin: 30px 0;">
            <div style="flex: 1; text-align: center;">
                <h2>WordPress 生成的头像</h2>
                <div style="width: 300px; height: 300px; border: 3px solid #0073aa; margin: 0 auto; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                    <?php echo $svg; ?>
                </div>
            </div>
            
            <div style="flex: 1; text-align: center;">
                <h2>官网生成的头像</h2>
                <div style="width: 300px; height: 300px; border: 3px solid #d63638; margin: 0 auto; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                    <div style="text-align: center;">
                        <p><a href="https://multiavatar.com/<?php echo urlencode($test_text); ?>" target="_blank" style="font-size: 14px;">
                            点击查看官网头像
                        </a></p>
                        <p style="font-size: 12px; color: #666;">
                            multiavatar.com/<?php echo esc_html($test_text); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 计算过程 -->
        <h2>📊 计算过程</h2>
        <table class="widefat">
            <tbody>
                <tr>
                    <th width="30%">输入文本</th>
                    <td><strong style="color: #0073aa; font-size: 16px;"><?php echo esc_html($test_text); ?></strong></td>
                </tr>
                <tr>
                    <th>SHA256 哈希</th>
                    <td style="font-size: 11px; word-break: break-all;"><?php echo esc_html($sha256); ?></td>
                </tr>
                <tr>
                    <th>提取数字</th>
                    <td><?php echo esc_html($numbers); ?></td>
                </tr>
                <tr>
                    <th>前 12 位</th>
                    <td><strong style="color: #0073aa; font-size: 16px;"><?php echo esc_html($hash_12); ?></strong></td>
                </tr>
            </tbody>
        </table>
        
        <!-- SVG 分析 -->
        <h2>🔍 SVG 分析</h2>
        <table class="widefat">
            <tbody>
                <tr>
                    <th width="30%">SVG 长度</th>
                    <td><?php echo strlen($svg); ?> 字节</td>
                </tr>
                <tr>
                    <th>包含 svg 标签</th>
                    <td><?php echo strpos($svg, '<svg') !== false ? '✅ 是' : '❌ 否'; ?></td>
                </tr>
                <tr>
                    <th>包含 xmlns</th>
                    <td><?php echo strpos($svg, 'xmlns') !== false ? '✅ 是' : '❌ 否'; ?></td>
                </tr>
                <tr>
                    <th>path 元素数量</th>
                    <td><?php echo substr_count($svg, '<path'); ?></td>
                </tr>
                <tr>
                    <th>SVG 前 200 字符</th>
                    <td><code style="font-size: 10px;"><?php echo esc_html(substr($svg, 0, 200)); ?>...</code></td>
                </tr>
            </tbody>
        </table>
        
        <!-- 缓存检查 -->
        <h2>💾 缓存检查</h2>
        <?php
        $cache_key = 'multiavatar_' . md5($test_text);
        $cached = get_transient($cache_key);
        ?>
        <table class="widefat">
            <tbody>
                <tr>
                    <th width="30%">缓存 Key</th>
                    <td><code><?php echo esc_html($cache_key); ?></code></td>
                </tr>
                <tr>
                    <th>是否有缓存</th>
                    <td><?php echo $cached ? '✅ 是' : '❌ 否'; ?></td>
                </tr>
                <?php if ($cached): ?>
                <tr>
                    <th>缓存内容长度</th>
                    <td><?php echo strlen($cached); ?> 字节</td>
                </tr>
                <tr>
                    <th>缓存与当前是否一致</th>
                    <td><?php echo ($cached === $svg) ? '✅ 一致' : '❌ 不一致'; ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- 清除缓存 -->
        <h2>🗑️ 清除缓存</h2>
        <form method="post">
            <?php wp_nonce_field('mav_clear_cache'); ?>
            <input type="hidden" name="clear_cache" value="1">
            <button type="submit" class="button button-secondary">清除所有 Multiavatar 缓存</button>
        </form>
        
        <?php
        if (isset($_POST['clear_cache']) && check_admin_referer('mav_clear_cache')) {
            global $wpdb;
            $count = $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_multiavatar_%'");
            $count2 = $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_multiavatar_%'");
            echo '<div class="notice notice-success"><p>✅ 已清除 ' . ($count + $count2) . ' 个缓存记录</p></div>';
        }
        ?>
        
        <!-- SVG 源码 -->
        <h2>📄 完整 SVG 源码</h2>
        <textarea style="width: 100%; height: 400px; font-family: monospace; font-size: 10px;" readonly><?php echo esc_textarea($svg); ?></textarea>
        
        <!-- 下载 SVG -->
        <p style="margin-top: 20px;">
            <a href="data:image/svg+xml;charset=utf-8,<?php echo urlencode($svg); ?>"
               download="multiavatar-<?php echo esc_attr($test_text); ?>.svg"
               class="button">
                💾 下载 SVG 文件
            </a>
        </p>
    </div>
    <?php
}
