# Multiavatar WordPress 插件

[![WordPress 插件](https://img.shields.io/badge/WordPress-插件-blue.svg)](https://wordpress.org/)
[![许可证: MIT](https://img.shields.io/badge/许可证-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Multiavatar](https://img.shields.io/badge/Multiavatar-官方-green.svg)](https://multiavatar.com)

**[English](README.md) | [简体中文](#简体中文)**

---

## 简体中文

一个集成 [Multiavatar](https://multiavatar.com) 的 WordPress 插件，用于生成独特的 SVG 头像。支持短代码、自动头像替换等功能。

## 🌟 功能特性

- ✅ **官方 Multiavatar 库** - 使用官方 Multiavatar PHP 库
- ✅ **48 种独特设计** - 机器人、女孩、外星人、熊、鸟等
- ✅ **120 亿种组合** - 可生成 12,230,590,464 个唯一头像
- ✅ **确定性算法** - 相同文本总是生成相同头像
- ✅ **SVG 格式** - 任意缩放不失真
- ✅ **短代码支持** - 在文章和页面中轻松使用
- ✅ **头像替换** - 自动替换 WordPress 默认头像
- ✅ **设置页面** - 用户友好的配置界面
- ✅ **缓存支持** - 内置缓存提高性能
- ✅ **多语言支持** - 支持多种语言

## 📦 安装

### 方法 1：WordPress 后台上传（推荐）

1. 下载最新版本
2. WordPress 后台 → 插件 → 安装插件 → 上传插件
3. 选择 ZIP 文件并点击"立即安装"
4. 激活插件

### 方法 2：手动安装

1. 将 `multiavatar-wordpress` 文件夹上传到 `/wp-content/plugins/`
2. 在 WordPress 后台 → 插件 中激活插件

### 方法 3：Git 克隆

```bash
cd /wp-content/plugins/
git clone https://github.com/ActonMartin/multiavatarForWordpress.git
```

## 🚀 使用方法

### 1. 短代码

在文章、页面或小工具中使用：

```
[multiavatar text="用户名" size="80"]
```

**参数说明：**

| 参数 | 说明 | 默认值 |
|------|------|--------|
| text | 生成头像的文本（用户名、邮箱等） | 当前用户 |
| size | 头像大小（像素） | 80 |
| class | CSS 类名 | multiavatar |
| id | 元素 ID | 空 |

**示例：**

```
// 显示当前用户的头像
[multiavatar]

// 显示指定用户的头像
[multiavatar text="john"]

// 自定义大小
[multiavatar text="user@example.com" size="120"]

// 添加自定义类和 ID
[multiavatar text="john" class="my-avatar" id="user-avatar"]
```

### 2. 自动头像替换

插件会自动替换 WordPress 默认头像：

- 评论作者头像
- 用户资料头像
- 管理工具栏头像
- 任何使用 `get_avatar()` 的地方

### 3. 设置页面

在 WordPress 后台 → Multiavatar 配置选项：

- **替换用户头像** - 启用/禁用自动替换
- **默认头像大小** - 设置默认尺寸（像素）
- **启用短代码** - 启用/禁用短代码功能
- **缓存头像** - 启用/禁用缓存以提高性能
- **清除缓存** - 清除所有缓存的头像

## 🔧 工作原理

### 算法流程

```
输入文本（如："actonmartin"）
    ↓
SHA256 哈希（64位十六进制）
    ↓
仅提取数字字符
    ↓
取前 12 位数字
    ↓
计算 6 个部分（env, clo, head, mouth, eyes, top）
    ↓
每部分：设计（00-47）+ 主题（A/B/C）
    ↓
生成 SVG 头像
```

### 示例

输入 "actonmartin" 的计算过程：

```
SHA256: 3cfff959bd30890d2ffe147edadd0b2012976afcb0b120fd8db721b9822b9743
前 12 位数字: 395930890214

各部分设计:
- env:   设计 02, 主题 B
- clo:   设计 12, 主题 B
- head:  设计 14, 主题 A
- mouth: 设计 10, 主题 C
- eyes:  设计 01, 主题 A
- top:   设计 07, 主题 A
```

## 🎨 自定义

### CSS 样式

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
// 获取插件实例
$plugin = Multiavatar_WordPress::get_instance();

// 生成头像 SVG
$svg = $plugin->generate_avatar_svg('username');

// 生成头像 HTML
$html = $plugin->generate_avatar_html('username', 80, 'avatar-class');
```

### JavaScript API

```javascript
// 生成头像
const svg = MultiavatarWP.generate('username', 100);

// 通过 AJAX 生成
MultiavatarWP.generateAjax('username', 100, function(html) {
    console.log(html);
});

// 替换元素内容
MultiavatarWP.replace('#avatar-container', 'username', 100);
```

## 🔍 故障排除

### 头像与 multiavatar.com 不一致

**原因：** 缓存了旧的头像数据

**解决方法：**
1. WordPress 后台 → Multiavatar
2. 点击"清除缓存"按钮
3. 刷新页面（Ctrl+F5）

### 头像不显示

**检查：**
1. 插件是否已激活
2. 短代码语法是否正确
3. 浏览器控制台是否有 JavaScript 错误
4. Multiavatar.php 文件是否存在（应为 79KB）

### 缓存无法清除

**解决方法：**
1. 使用诊断工具（`mav-diagnostic.php`）
2. 或直接执行 SQL：
```sql
DELETE FROM wp_options WHERE option_name LIKE '_transient_multiavatar_%';
```

## 📊 性能

- **缓存：** 默认缓存 30 天
- **SVG 大小：** 平均 2-5KB 每个头像
- **生成时间：** < 10ms 每个头像
- **内存使用：** 极小（无外部 API 调用）

## 🌐 兼容性

- **WordPress：** 5.0 或更高版本
- **PHP：** 7.4 或更高版本
- **浏览器：** 所有现代浏览器（需要 SVG 支持）

## 📝 更新日志

### 版本 1.0.0（当前版本）

- ✅ 首次发布
- ✅ 集成官方 Multiavatar 库
- ✅ 短代码支持
- ✅ 头像替换
- ✅ 设置页面
- ✅ 缓存系统
- ✅ 诊断工具

## 🤝 贡献

欢迎贡献！请阅读 [CONTRIBUTING.md](CONTRIBUTING.md) 了解详情。

### 开发

```bash
# 克隆仓库
git clone https://github.com/ActonMartin/multiavatarForWordpress.git

# 创建分支
git checkout -b feature/your-feature

# 进行修改并提交
git commit -m "添加你的功能"

# 推送并创建 PR
git push origin feature/your-feature
```

## 📄 许可证

本项目采用 MIT 许可证 - 详见 [LICENSE](LICENSE) 文件。

## 🙏 致谢

- [Multiavatar](https://multiavatar.com) - 头像生成库
- [WordPress](https://wordpress.org) - 本插件所基于的平台

## 💖 支持项目

如果这个插件对您有帮助，请考虑支持开发：

### 赞助

![赞助二维码](微信支付宝合并收款码.png)

*微信 / 支付宝赞赏码*

### 其他支持方式

- ⭐ 给仓库加星
- 🐛 报告 Bug
- 💡 建议新功能
- 📖 改进文档
- 🔀 提交 Pull Request

## 📞 支持

- **文档：** [README.md](README.md) (English)
- **问题：** [GitHub Issues](https://github.com/ActonMartin/multiavatarForWordpress/issues)
- **Multiavatar 官方：** [multiavatar.com](https://multiavatar.com)

## 🗺️ 开发路线

- [ ] 提交到 WordPress.org 插件目录
- [ ] Gutenberg 块支持
- [ ] REST API 端点
- [ ] 批量头像生成
- [ ] 头像画廊短代码
- [ ] 自定义颜色主题
- [ ] 头像导出功能

---

**用 ❤️ 制作 by Multiavatar 社区**

[网站](https://multiavatar.com) • [文档](README.md) • [报告 Bug](https://github.com/ActonMartin/multiavatarForWordpress/issues) • [功能建议](https://github.com/ActonMartin/multiavatarForWordpress/issues)
