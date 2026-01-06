# NCAC Installation Guide

Complete guide to install and configure the NCAC PHPCS Standard in your project.

## Quick Installation

```bash
composer require --dev ncac/phpcs-standard
```

That's it! The `ncac-format` command is now available in `vendor/bin/`.

## Usage

### Basic Commands

```bash
# Format all files in src/
vendor/bin/ncac-format src/

# Preview changes without applying them
vendor/bin/ncac-format --dry-run src/

# Format current directory
vendor/bin/ncac-format

# Show help
vendor/bin/ncac-format --help
```

### With Composer Scripts (Recommended)

Add to your `composer.json`:

```json
{
  "scripts": {
    "format": "ncac-format",
    "format:dry": "ncac-format --dry-run",
    "check": "phpcs --standard=NCAC"
  }
}
```

Then use:

```bash
composer format      # Format code
composer format:dry  # Preview changes
composer check       # Validate code
```

## Configuration

### Method 1: Use Default Configuration

No configuration needed! The package works out of the box with sensible defaults.

### Method 2: Custom PHP-CS-Fixer Config

Create `.php-cs-fixer.php` at your project root:

```php
<?php

$finder = PhpCsFixer\Finder::create()
  ->in(__DIR__ . '/src')
  ->in(__DIR__ . '/tests')
  ->exclude('vendor');

$config = require __DIR__ . '/vendor/ncac/phpcs-standard/.php-cs-fixer.dist.php';
$config->setFinder($finder);

return $config;
```

### Method 3: Custom PHPCS Config

Create `phpcs.xml` at your project root:

```xml
<?xml version="1.0"?>
<ruleset name="My Project">
  <description>My coding standard based on NCAC</description>

  <rule ref="NCAC"/>

  <file>src/</file>
  <file>tests/</file>

  <exclude-pattern>*/vendor/*</exclude-pattern>
  <exclude-pattern>*/cache/*</exclude-pattern>
</ruleset>
```

## CI/CD Integration

### GitHub Actions

`.github/workflows/code-quality.yml`:

```yaml
name: Code Quality

on: [push, pull_request]

jobs:
  format-check:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      
      - name: Install dependencies
        run: composer install
      
      - name: Check formatting
        run: vendor/bin/ncac-format --dry-run src/
      
      - name: Validate with PHPCS
        run: vendor/bin/phpcs --standard=NCAC src/
```

### GitLab CI

`.gitlab-ci.yml`:

```yaml
code-quality:
  image: php:8.1-cli
  before_script:
    - curl -sS https://getcomposer.org/installer | php
    - php composer.phar install
  script:
    - vendor/bin/ncac-format --dry-run src/
    - vendor/bin/phpcs --standard=NCAC src/
```

### Pre-commit Hook

`.git/hooks/pre-commit`:

```bash
#!/bin/bash
FILES=$(git diff --cached --name-only --diff-filter=ACM | grep '\.php$')

if [ ! -z "$FILES" ]; then
  vendor/bin/ncac-format --dry-run $FILES
  if [ $? -ne 0 ]; then
    echo "❌ Formatting issues detected. Run: vendor/bin/ncac-format"
    exit 1
  fi
fi

echo "✅ Code formatting OK"
exit 0
```

Make executable:

```bash
chmod +x .git/hooks/pre-commit
```

## Global Installation (Optional)

Install globally for all projects:

```bash
composer global require ncac/phpcs-standard
```

Add to PATH (if not already):

```bash
export PATH="$PATH:$HOME/.composer/vendor/bin"
```

Use anywhere:

```bash
ncac-format src/
```

## Troubleshooting

### Command not found

1. Verify installation:
   ```bash
   composer show ncac/phpcs-standard
   ```

2. Regenerate autoloader:
   ```bash
   composer dump-autoload
   ```

### No config warning

This is normal. The tool will use default configuration. To suppress, create a `.php-cs-fixer.php` file.

### Files not formatted

1. Check you're not using `--dry-run`
2. Verify file permissions
3. Check output for errors

### Slow performance

For large projects:

```bash
vendor/bin/ncac-format src/     # Format src only
vendor/bin/ncac-format tests/   # Format tests only
```

## Verification

```bash
# Check command is available
vendor/bin/ncac-format --help

# Check PHPCS standard is registered
vendor/bin/phpcs -i  # Should show: NCAC

# Test on a file
vendor/bin/ncac-format --dry-run src/MyClass.php
```

## Additional Resources

- [Quick Start Guide](./QUICK_START.md)
- [Command Reference](./NCAC_FORMAT_COMMAND.md)
- [Migration Guide](./MIGRATION_NCAC_FIX_TO_FORMAT.md)
- [GitHub Repository](https://github.com/ncac/phpcs-standard)

## Support

- **Issues**: https://github.com/ncac/phpcs-standard/issues
- **Discussions**: https://github.com/ncac/phpcs-standard/discussions
- **Documentation**: https://github.com/ncac/phpcs-standard/tree/main/docs
