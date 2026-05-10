/**
 * Multiavatar WordPress Frontend Script
 */
(function($) {
    'use strict';

    // 初始化
    $(document).ready(function() {
        // 为所有 multiavatar 元素生成头像
        $('.multiavatar-placeholder').each(function() {
            const $el = $(this);
            const text = $el.data('text');
            const size = $el.data('size') || 80;
            
            if (text && typeof multiavatar !== 'undefined') {
                const svg = multiavatar(text, size);
                $el.replaceWith(svg);
            }
        });
    });

    // 暴露全局方法
    window.MultiavatarWP = {
        /**
         * 生成头像
         */
        generate: function(text, size) {
            if (typeof multiavatar === 'undefined') {
                console.error('Multiavatar library not loaded');
                return null;
            }
            return multiavatar(text, size);
        },

        /**
         * 通过 AJAX 生成头像
         */
        generateAjax: function(text, size, callback) {
            $.ajax({
                url: multiavatarWp.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'multiavatar_generate',
                    nonce: multiavatarWp.nonce,
                    text: text,
                    size: size
                },
                success: function(response) {
                    if (response.success && callback) {
                        callback(response.data.html);
                    }
                },
                error: function() {
                    console.error('Failed to generate avatar');
                }
            });
        },

        /**
         * 替换元素内容为头像
         */
        replace: function(selector, text, size) {
            const svg = this.generate(text, size);
            if (svg) {
                $(selector).html(svg);
            }
        }
    };

})(jQuery);
