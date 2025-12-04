# Breaking Changes in v2.0.0

## 🚨 PHP Version Requirement Changed

**Previous versions (v1.2.0 and below):** PHP ^7.4 || ^8.0  
**New requirement (v2.0.0+):** PHP ^8.1+

### Why This Change?

1. **Dependency Requirements**: Modern tooling dependencies (PHPUnit 10, Phing 3, Symfony 6/7) require PHP 8.1+
2. **PHP 8.0 EOL**: PHP 8.0 reached End of Life in November 2023 and no longer receives security updates
3. **PHP 7.4 EOL**: PHP 7.4 reached End of Life in November 2022
4. **Modern PHP Features**: PHP 8.1+ provides enums, readonly properties, intersection types, and other features that align with our TypeScript-inspired approach
5. **Industry Standard**: PHP 8.1+ is now the baseline for modern PHP projects (Laravel 10+, Symfony 6+)

### Migration Guide

#### For Projects on PHP 7.4

**Option 1: Upgrade PHP (Recommended)**
```bash
# Stay on v1.2.0 until you can upgrade
composer require ncac/phpcs-standard:^1.2.0

# Then upgrade PHP and update to v2.0.0
composer require ncac/phpcs-standard:^2.0
```

**Option 2: Stay on v1.x**
```bash
# Pin to the last PHP 7.4-compatible version
composer require ncac/phpcs-standard:^1.2.0
```

#### For Projects on PHP 8.0

**Option 1: Upgrade PHP (Recommended)**
```bash
# Stay on v1.2.0 until you can upgrade
composer require ncac/phpcs-standard:^1.2.0

# Then upgrade PHP to 8.1+ and update to v2.0.0
composer require ncac/phpcs-standard:^2.0
```

**Option 2: Stay on v1.x**
```bash
# Pin to the last PHP 7.4/8.0-compatible version
composer require ncac/phpcs-standard:^1.2.0
```

#### For Projects on PHP 8.1+

Simply update:
```bash
composer require ncac/phpcs-standard:^2.0
```

### What Stayed The Same

- ✅ All coding rules remain identical
- ✅ No changes to sniff behavior
- ✅ All existing configurations work as-is
- ✅ PHPCS ruleset format unchanged

### Timeline

- **v1.2.0** (2025-11-05): Last version supporting PHP 7.4
- **v2.0.0** (2025-12-04): Official PHP 8.1+ requirement with all fixes and improvements

## Need Help?

If you have questions about this migration, please open an issue:
https://github.com/ncac/phpcs-standard/issues
