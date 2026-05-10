# Contributing to Multiavatar for WordPress

First off, thank you for considering contributing to Multiavatar for WordPress! 🎉

## 📜 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Commit Guidelines](#commit-guidelines)
- [Pull Request Process](#pull-request-process)
- [Reporting Bugs](#reporting-bugs)
- [Suggesting Features](#suggesting-features)

## 📜 Code of Conduct

This project and everyone participating in it is governed by the [Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code. Please report unacceptable behavior to the project maintainers.

## 🤝 How Can I Contribute?

### Reporting Bugs

Bug reports are incredibly helpful. Please use the [GitHub Issues](https://github.com/yourusername/multiavatar-wordpress/issues) page.

### Suggesting Enhancements

Feature suggestions are welcome! Please use the [GitHub Issues](https://github.com/yourusername/multiavatar-wordpress/issues) page with the "enhancement" label.

### Pull Requests

Pull requests are welcome! Please follow the [Pull Request Process](#pull-request-process).

### Documentation

Improvements to documentation are always welcome. This includes:
- README improvements
- Code comments
- Usage examples
- Translation updates

## 🛠️ Development Setup

### Prerequisites

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Git
- A local WordPress development environment

### Setup Steps

1. **Fork the repository**
   ```bash
   # Click "Fork" on GitHub
   ```

2. **Clone your fork**
   ```bash
   git clone https://github.com/your-username/multiavatar-wordpress.git
   cd multiavatar-wordpress
   ```

3. **Create a branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

4. **Install in WordPress**
   ```bash
   # Symlink or copy to wp-content/plugins/
   ln -s $(pwd) /path/to/wordpress/wp-content/plugins/multiavatar-wordpress
   ```

5. **Activate in WordPress**
   - Go to WordPress Admin → Plugins
   - Activate "Multiavatar for WordPress"

## 📏 Coding Standards

### PHP Standards

We follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/):

- Use tabs for indentation
- Proper spacing around operators
- Yoda conditions for comparisons
- Meaningful variable names
- Document all functions

### Example

```php
/**
 * Generate avatar SVG
 *
 * @param string $text Text to generate avatar from
 * @return string SVG code
 */
public function generate_avatar_svg( $text ) {
    if ( empty( $text ) ) {
        return '';
    }
    
    $multiavatar = new Multiavatar();
    $svg = $multiavatar( $text, null, null );
    
    return $svg;
}
```

### JavaScript Standards

- Use ES6+ features
- Follow [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/)
- Use meaningful variable names
- Comment complex logic

### CSS Standards

- Follow [WordPress CSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/)
- Use lowercase
- Use hyphens for multi-word selectors
- Organize properties alphabetically

## 📝 Commit Guidelines

### Commit Message Format

```
type(scope): subject

body

footer
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Maintenance tasks

### Examples

```
feat(cache): add cache clearing functionality

Add ability to clear all cached avatars from settings page.
Includes confirmation dialog and success message.

Closes #123
```

```
fix(avatar): correct SVG generation for special characters

Escape special characters in text before generating avatar.
This fixes issues with non-ASCII characters.

Fixes #456
```

## 🔄 Pull Request Process

1. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature
   ```

2. **Make your changes**
   - Follow coding standards
   - Add tests if applicable
   - Update documentation

3. **Test your changes**
   - Test in multiple WordPress versions
   - Test with different PHP versions
   - Verify no errors in debug mode

4. **Commit your changes**
   ```bash
   git add .
   git commit -m "feat: your feature description"
   ```

5. **Push to your fork**
   ```bash
   git push origin feature/your-feature
   ```

6. **Create Pull Request**
   - Go to GitHub
   - Click "New Pull Request"
   - Fill in the template
   - Submit

### PR Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
- [ ] Tested in WordPress 5.x
- [ ] Tested in WordPress 6.x
- [ ] Tested with PHP 7.4
- [ ] Tested with PHP 8.x

## Checklist
- [ ] Code follows coding standards
- [ ] Documentation updated
- [ ] No new warnings
- [ ] Tests added/updated
```

## 🐛 Reporting Bugs

### Before Reporting

1. Check existing issues
2. Test with latest version
3. Disable other plugins
4. Check error logs

### Bug Report Template

```markdown
## Description
Clear description of the bug

## Steps to Reproduce
1. Go to '...'
2. Click on '....'
3. See error

## Expected Behavior
What should happen

## Actual Behavior
What actually happens

## Screenshots
If applicable

## Environment
- WordPress version: 
- PHP version: 
- Plugin version: 
- Browser: 

## Additional Context
Any other relevant information
```

## 💡 Suggesting Features

### Feature Request Template

```markdown
## Problem
Description of the problem this feature would solve

## Solution
Description of the proposed solution

## Alternatives
Alternative solutions considered

## Additional Context
Any other relevant information

## Would you be willing to implement this?
- [ ] Yes, I can implement this
```

## 🌍 Translations

We welcome translations! 

### How to Translate

1. Create a `.po` file in `/languages/`
2. Use [Poedit](https://poedit.net/) or similar tool
3. Translate strings
4. Generate `.mo` file
5. Submit PR

### Available Languages

- English (default)
- Chinese (简体中文)

## 📚 Resources

- [WordPress Plugin Developer Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [Multiavatar Documentation](https://multiavatar.com)

## 🙏 Thank You!

Your contributions make this project better for everyone. Thank you for your time and effort!

---

**Questions?** Feel free to [open an issue](https://github.com/yourusername/multiavatar-wordpress/issues) or reach out to the maintainers.
