# Multiavatar for WordPress

[![WordPress Plugin](https://img.shields.io/badge/WordPress-Plugin-blue.svg)](https://wordpress.org/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Multiavatar](https://img.shields.io/badge/Multiavatar-Official-green.svg)](https://multiavatar.com)

**[English](#english) | [简体中文](README_CN.md)**

---

## English

A WordPress plugin that integrates [Multiavatar](https://multiavatar.com) to generate unique SVG avatars. Supports shortcodes, automatic avatar replacement, and more.

## 🌟 Features

- ✅ **Official Multiavatar Library** - Uses the official Multiavatar PHP library
- ✅ **48 Unique Designs** - Robots, girls, aliens, bears, birds, and more
- ✅ **12+ Billion Combinations** - 12,230,590,464 unique avatars possible
- ✅ **Deterministic Algorithm** - Same text always generates the same avatar
- ✅ **SVG Format** - Scalable without quality loss
- ✅ **Shortcode Support** - Easy to use in posts and pages
- ✅ **Avatar Replacement** - Automatically replace WordPress default avatars
- ✅ **Settings Page** - Configure options with a user-friendly interface
- ✅ **Caching Support** - Improve performance with built-in caching
- ✅ **Multilingual** - Supports multiple languages

## 📦 Installation

### Method 1: WordPress Plugin Upload (Recommended)

1. Download the latest release
2. Go to WordPress Admin → Plugins → Add New → Upload Plugin
3. Choose the ZIP file and click "Install Now"
4. Activate the plugin

### Method 2: Manual Installation

1. Upload the `multiavatar-wordpress` folder to `/wp-content/plugins/`
2. Activate the plugin through the WordPress Admin → Plugins menu

### Method 3: Git Clone

```bash
cd /wp-content/plugins/
git clone https://github.com/yourusername/multiavatar-wordpress.git
```

## 🚀 Usage

### 1. Shortcode

Use in posts, pages, or widgets:

```
[multiavatar text="username" size="80"]
```

**Parameters:**

| Parameter | Description | Default |
|-----------|-------------|---------|
| text | Text to generate avatar from (username, email, etc.) | Current user |
| size | Avatar size in pixels | 80 |
| class | CSS class name | multiavatar |
| id | Element ID | empty |

**Examples:**

```
// Display current user's avatar
[multiavatar]

// Display specific user's avatar
[multiavatar text="john"]

// Custom size
[multiavatar text="user@example.com" size="120"]

// With custom class and ID
[multiavatar text="john" class="my-avatar" id="user-avatar"]
```

### 2. Automatic Avatar Replacement

The plugin automatically replaces WordPress default avatars:

- Comment author avatars
- User profile avatars
- Admin bar avatars
- Any place using `get_avatar()`

### 3. Settings Page

Configure options at WordPress Admin → Multiavatar:

- **Replace User Avatars** - Enable/disable automatic replacement
- **Default Avatar Size** - Set default size in pixels
- **Enable Shortcode** - Enable/disable shortcode functionality
- **Cache Avatars** - Enable/disable caching for better performance
- **Clear Cache** - Clear all cached avatars

## 🔧 How It Works

### Algorithm

```
Input Text (e.g., "actonmartin")
    ↓
SHA256 Hash (64-character hex)
    ↓
Extract Numbers Only
    ↓
Take First 12 Digits
    ↓
Calculate 6 Parts (env, clo, head, mouth, eyes, top)
    ↓
Each Part: Design (00-47) + Theme (A/B/C)
    ↓
Generate SVG Avatar
```

### Example

For input "actonmartin":

```
SHA256: 3cfff959bd30890d2ffe147edadd0b2012976afcb0b120fd8db721b9822b9743
First 12 digits: 395930890214

Parts:
- env:   Design 02, Theme B
- clo:   Design 12, Theme B
- head:  Design 14, Theme A
- mouth: Design 10, Theme C
- eyes:  Design 01, Theme A
- top:   Design 07, Theme A
```

## 🎨 Customization

### CSS Styling

```css
.multiavatar {
    border-radius: 50%;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.multiavatar:hover {
    transform: scale(1.1);
}
```

### PHP API

```php
// Get plugin instance
$plugin = Multiavatar_WordPress::get_instance();

// Generate avatar SVG
$svg = $plugin->generate_avatar_svg('username');

// Generate avatar HTML
$html = $plugin->generate_avatar_html('username', 80, 'avatar-class');
```

### JavaScript API

```javascript
// Generate avatar
const svg = MultiavatarWP.generate('username', 100);

// Generate via AJAX
MultiavatarWP.generateAjax('username', 100, function(html) {
    console.log(html);
});

// Replace element content
MultiavatarWP.replace('#avatar-container', 'username', 100);
```

## 🔍 Troubleshooting

### Avatar doesn't match multiavatar.com

**Cause:** Cached old avatar data

**Solution:**
1. Go to WordPress Admin → Multiavatar
2. Click "Clear Cache" button
3. Refresh the page (Ctrl+F5)

### Avatar not displaying

**Check:**
1. Plugin is activated
2. Shortcode syntax is correct
3. No JavaScript errors in browser console
4. Multiavatar.php file exists (should be 79KB)

### Cache won't clear

**Solution:**
1. Use the diagnostic tool (`mav-diagnostic.php`)
2. Or execute SQL directly:
```sql
DELETE FROM wp_options WHERE option_name LIKE '_transient_multiavatar_%';
```

## 📊 Performance

- **Caching:** Avatars are cached for 30 days by default
- **SVG Size:** Average 2-5KB per avatar
- **Generation Time:** < 10ms per avatar
- **Memory Usage:** Minimal (no external API calls)

## 🌐 Compatibility

- **WordPress:** 5.0 or higher
- **PHP:** 7.4 or higher
- **Browsers:** All modern browsers (SVG support required)

## 📝 Changelog

### Version 1.0.0 (Current)

- ✅ Initial release
- ✅ Official Multiavatar library integration
- ✅ Shortcode support
- ✅ Avatar replacement
- ✅ Settings page
- ✅ Caching system
- ✅ Diagnostic tools

## 🤝 Contributing

Contributions are welcome! Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development

```bash
# Clone the repository
git clone https://github.com/yourusername/multiavatar-wordpress.git

# Create a branch
git checkout -b feature/your-feature

# Make changes and commit
git commit -m "Add your feature"

# Push and create PR
git push origin feature/your-feature
```

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Credits

- [Multiavatar](https://multiavatar.com) - The avatar generation library
- [WordPress](https://wordpress.org) - The platform this plugin is built for

## 💖 Support the Project

If this plugin helps you, consider supporting its development:

### Sponsor

![Sponsor QR Code](微信支付宝合并收款码.png)

*WeChat / Alipay donation QR code*

### Other Ways to Support

- ⭐ Star the repository
- 🐛 Report bugs
- 💡 Suggest features
- 📖 Improve documentation
- 🔀 Submit pull requests

## 📞 Support

- **Documentation:** [README_CN.md](README_CN.md) (中文文档)
- **Issues:** [GitHub Issues](https://github.com/yourusername/multiavatar-wordpress/issues)
- **Multiavatar Official:** [multiavatar.com](https://multiavatar.com)

## 🗺️ Roadmap

- [ ] WordPress.org plugin directory submission
- [ ] Gutenberg block support
- [ ] REST API endpoint
- [ ] Bulk avatar generation
- [ ] Avatar gallery shortcode
- [ ] Custom color themes
- [ ] Avatar export feature

---

**Made with ❤️ by the Multiavatar Community**

[Website](https://multiavatar.com) • [Documentation](README_CN.md) • [Report Bug](https://github.com/yourusername/multiavatar-wordpress/issues) • [Request Feature](https://github.com/yourusername/multiavatar-wordpress/issues)
