# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- WordPress.org plugin directory submission (planned)
- Gutenberg block support (planned)
- REST API endpoint (planned)
- Bulk avatar generation (planned)
- Avatar gallery shortcode (planned)
- Custom color themes (planned)
- Avatar export feature (planned)

## [1.0.1] - 2024-05-07

### Fixed
- 🚨 **CRITICAL**: Fixed TypeError when handling WP_Comment objects
- Fixed `is_email()` being called on non-string values
- Added proper type checking before calling `is_email()`
- Added support for WP_Comment objects with `comment_author_email`
- Added property existence checks for object handling

### Changed
- Improved `get_user_identifier_from_id()` method type safety
- Better handling of different WordPress object types (WP_User, WP_Post, WP_Comment)

## [1.0.0] - 2024-05-07

### Added
- ✅ Initial release
- ✅ Official Multiavatar PHP library integration (v1.0)
- ✅ Shortcode support `[multiavatar]`
- ✅ Automatic WordPress avatar replacement
- ✅ Settings page with configuration options
- ✅ Caching system for better performance
- ✅ Cache clearing functionality
- ✅ Diagnostic tools for troubleshooting
- ✅ Force cache clearing tool
- ✅ Detailed calculation process display
- ✅ SHA256 hash display
- ✅ 12-digit hash display
- ✅ Part design breakdown (env, clo, head, mouth, eyes, top)
- ✅ Multi-language support (English, Chinese)
- ✅ Comprehensive documentation
- ✅ MIT License

### Features
- 48 unique avatar designs
- 12,230,590,464 possible unique avatars
- Deterministic algorithm (same input = same output)
- SVG format (scalable without quality loss)
- No external API dependencies
- Fast generation (< 10ms per avatar)
- Small file size (2-5KB per avatar)

### Security
- Input sanitization for all user inputs
- Nonce verification for admin actions
- Escape all output to prevent XSS
- Check user capabilities before admin actions

### Performance
- Transient API caching (30 days by default)
- Cache validation to ensure valid SVG
- Minimal memory footprint
- No external HTTP requests

### Documentation
- README.md (English)
- README_CN.md (Chinese)
- Installation guide
- Troubleshooting guide
- API documentation
- Examples and usage

### Developer Tools
- `mav-diagnostic.php` - Diagnostic tool
- `mav-force-clear.php` - Force cache clearing
- `clear-cache.sql` - SQL cache clearing script
- PHP API for programmatic access
- JavaScript API for frontend integration

## [0.9.0] - 2024-05-07 (Pre-release)

### Added
- Basic plugin structure
- Simple avatar generation (deprecated)
- Basic shortcode functionality

### Changed
- Replaced simple generation with official Multiavatar library
- Improved caching mechanism
- Enhanced security measures

### Fixed
- Cache validation issues
- Path resolution problems
- SVG generation inconsistencies

---

## Version History

| Version | Date | Description |
|---------|------|-------------|
| 1.0.0 | 2024-05-07 | First stable release |
| 0.9.0 | 2024-05-07 | Pre-release version |

---

## Upgrade Guide

### From 0.9.0 to 1.0.0

1. **Backup your data** (optional, no database changes)
2. **Deactivate** the old version
3. **Delete** old plugin files
4. **Upload** new version
5. **Activate** the plugin
6. **Clear cache** in settings
7. **Verify** avatars match multiavatar.com

### Important Notes

- Version 1.0.0 uses the official Multiavatar library
- Old cached avatars will be automatically regenerated
- No data loss during upgrade
- Settings are preserved

---

## Deprecation Notice

### v0.9.0 Features (Deprecated)

The following features from v0.9.0 are deprecated and replaced:

- Simple avatar generation → Official Multiavatar library
- MD5 hashing → SHA256 hashing
- Basic caching → Enhanced caching with validation

---

[Unreleased]: https://github.com/yourusername/multiavatar-wordpress/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/yourusername/multiavatar-wordpress/releases/tag/v1.0.0
[0.9.0]: https://github.com/yourusername/multiavatar-wordpress/releases/tag/v0.9.0
