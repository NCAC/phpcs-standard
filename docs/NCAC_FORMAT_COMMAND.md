# NCAC Fix Command

## Installation

When you install the `ncac/phpcs-standard` package via Composer, the `ncac-format` command is automatically available in your project's `vendor/bin/` directory.

```bash
composer require --dev ncac/phpcs-standard
```

## Usage

After installation, you can run the NCAC code formatting workflow using:

```bash
vendor/bin/ncac-format [options] [path]
```

### Basic Examples

```bash
# Fix all files in current directory
vendor/bin/ncac-format

# Fix all files in src/ directory
vendor/bin/ncac-format src/

# Preview changes without applying them (dry run)
vendor/bin/ncac-format --dry-run src/

# Fix a specific file
vendor/bin/ncac-format src/MyClass.php

# Show help
vendor/bin/ncac-format --help
```

### Global Installation (Optional)

If you want to use `ncac-format` globally on your system:

```bash
composer global require ncac/phpcs-standard
```

Then you can run it directly:

```bash
ncac-format src/
```

## What Does It Do?

The `ncac-format` command runs a complete 3-step formatting workflow:

1. **PHP-CS-Fixer** - Applies complex code transformations and formatting
2. **PHPCBF** - Applies NCAC-specific code style fixes
3. **PHPCS** - Validates the code and reports remaining issues

## Options

- `--dry-run` - Preview changes without modifying files
- `--help` - Display help message
- `[path]` - Target path (file or directory). Defaults to current directory

## Exit Codes

- `0` - Success, no violations found
- `1` - Some violations remain after auto-fixing

## CI/CD Integration

You can integrate this into your CI/CD pipeline:

```yaml
# .github/workflows/code-quality.yml
- name: Run NCAC Code Quality
  run: vendor/bin/ncac-format --dry-run .
```

Or in your `composer.json`:

```json
{
  "scripts": {
    "fix": "vendor/bin/ncac-format",
    "fix:dry": "vendor/bin/ncac-format --dry-run"
  }
}
```

Then run:

```bash
composer fix
composer fix:dry
```
