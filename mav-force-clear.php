<?php
/**
 * Plugin Name: Multiavatar 强制清除缓存
 * Description: 强制清除所有 Multiavatar 缓存
 * Version: 1.0
 */

add_action('admin_menu', function() {
    add_menu_page('MAV 清缓存', 'MAV 清缓存', 'manage_options', 'mav-force-clear', 'mav_force_clear_page', 'dashicons-trash', 99);
});

function mav_force_clear_page() {
    global $wpdb;
    
    // 强制清除缓存
    if (isset($_POST['force_clear']) && check_admin_referer('mav_force_clear')) {
        // 方法1: 直接删除 transient
        $count1 = $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_multiavatar_%'");
        $count2 = $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_multiavatar_%'");
        
        // 方法2: 使用 WordPress API
        $all_transients = $wpdb->get_results("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_multiavatar_%'");
        $wp_count = 0;
        foreach ($all_transients as $transient) {
            $key = str_replace('_transient_', '', $transient->option_name);
            if (delete_transient($key)) {
                $wp_count++;
            }
        }
        
        echo '<div class="notice notice-success is-dismissible"><p>';
        echo "✅ 强制清除完成！<br>";
        echo "方法1 (SQL): 删除了 " . ($count1 + $count2) . " 条记录<br>";
        echo "方法2 (WP API): 删除了 {$wp_count} 条记录";
        echo '</p></div>';
    }
    
    // 查看当前缓存
    $caches = $wpdb->get_results("SELECT option_name, LENGTH(option_value) as size FROM {$wpdb->options} WHERE option_name LIKE '_transient_multiavatar_%' ORDER BY option_name");
    
    ?>
    <div class="wrap">
        <h1>🗑️ Multiavatar 强制清除缓存</h1>
        
        <!-- 当前缓存状态 -->
        <h2>📊 当前缓存状态</h2>
        <?php if (empty($caches)): ?>
            <p>✅ 没有缓存数据</p>
        <?php else: ?>
            <p>⚠️ 发现 <strong><?php echo count($caches); ?></strong> 个缓存记录：</p>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>缓存 Key</th>
                        <th>大小</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($caches as $cache): 
                        $key = str_replace('_transient_', '', $cache->option_name);
                    ?>
                        <tr>
                            <td><code><?php echo esc_html($key); ?></code></td>
                            <td><?php echo $cache->size; ?> 字节</td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <?php wp_nonce_field('mav_force_clear'); ?>
                                    <input type="hidden" name="delete_single" value="<?php echo esc_attr($key); ?>">
                                    <button type="submit" name="force_clear" class="button button-small">删除</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <!-- 强制清除所有 -->
        <h2>💣 强制清除所有缓存</h2>
        <div class="notice notice-warning">
            <p>这将删除所有 Multiavatar 缓存数据，强制重新生成头像。</p>
        </div>
        
        <form method="post">
            <?php wp_nonce_field('mav_force_clear'); ?>
            <button type="submit" name="force_clear" class="button button-primary button-large" onclick="return confirm('确定要清除所有缓存吗？');">
                🗑️ 强制清除所有缓存
            </button>
        </form>
        
        <!-- 手动 SQL -->
        <h2>🔧 手动 SQL 命令</h2>
        <p>如果上面的方法无效，可以在 phpMyAdmin 中执行：</p>
        <textarea style="width: 100%; height: 100px; font-family: monospace;" readonly>DELETE FROM <?php echo $wpdb->options; ?> WHERE option_name LIKE '_transient_multiavatar_%';
DELETE FROM <?php echo $wpdb->options; ?> WHERE option_name LIKE '_transient_timeout_multiavatar_%';</textarea>
        
        <!-- 验证缓存内容 -->
        <h2>🔍 检查特定缓存</h2>
        <form method="get">
            <input type="hidden" name="page" value="mav-force-clear">
            <label>缓存 Key：</label>
            <input type="text" name="check_key" value="<?php echo isset($_GET['check_key']) ? esc_attr($_GET['check_key']) : 'multiavatar_134ee58e4e15fde2c5f8ce23353b514b'; ?>" style="width: 400px;">
            <button type="submit" class="button">检查</button>
        </form>
        
        <?php if (isset($_GET['check_key'])): 
            $check_key = sanitize_text_field($_GET['check_key']);
            $cached = get_transient($check_key);
        ?>
            <h3>缓存内容：</h3>
            <?php if ($cached): ?>
                <p><strong>长度：</strong> <?php echo strlen($cached); ?> 字节</p>
                <p><strong>包含 svg 标签：</strong> <?php echo strpos($cached, '<svg') !== false ? '✅ 是' : '❌ 否'; ?></p>
                <p><strong>前 200 字符：</strong></p>
                <textarea style="width: 100%; height: 100px; font-family: monospace; font-size: 10px;" readonly><?php echo esc_textarea(substr($cached, 0, 200)); ?></textarea>
                
                <form method="post">
                    <?php wp_nonce_field('mav_force_clear'); ?>
                    <input type="hidden" name="delete_single" value="<?php echo esc_attr($check_key); ?>">
                    <button type="submit" name="force_clear" class="button button-secondary">删除此缓存</button>
                </form>
            <?php else: ?>
                <p>✅ 缓存不存在或已删除</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php
    
    // 删除单个缓存
    if (isset($_POST['delete_single']) && check_admin_referer('mav_force_clear')) {
        $key = sanitize_text_field($_POST['delete_single']);
        if (delete_transient($key)) {
            echo '<div class="notice notice-success"><p>✅ 已删除缓存: ' . esc_html($key) . '</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>❌ 删除失败: ' . esc_html($key) . '</p></div>';
        }
    }
}
