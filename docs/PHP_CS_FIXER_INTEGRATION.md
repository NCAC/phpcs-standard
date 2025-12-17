# PHP-CS-Fixer Integration for NCAC-based Projects

## Importing NCAC Configuration

```php
<?php
// .php-cs-fixer.php in your project root

// Import the base NCAC configuration
$ncacConfig = require 'vendor/ncac/phpcs-standard/.php-cs-fixer.dist.php';

// Customize the Finder for your project structure
$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/app',  
        __DIR__ . '/config',
    ])
    ->exclude([
        'vendor',
        'var/cache',
        'storage',
        'bootstrap/cache',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

// Override the finder while keeping NCAC rules intact
return $ncacConfig->setFinder($finder);
```

## Rule Customization (Optional)

```php
<?php
// If you need to modify specific NCAC rules

$ncacConfig = require 'vendor/ncac/phpcs-standard/.php-cs-fixer.dist.php';

$customRules = $ncacConfig->getRules();
// Disable a specific rule
unset($customRules['declare_strict_types']);
// Or modify a rule configuration
$customRules['concat_space'] = ['spacing' => 'none'];

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->name('*.php');

return $ncacConfig
    ->setRules($customRules)
    ->setFinder($finder);
```

## Framework-Specific Examples

### Laravel Projects
```php

use PhpCsFixer\Finder;

$finder = (new Finder())
  ->in([
    __DIR__ . '/app',
    __DIR__ . '/config',
    __DIR__ . '/database',
    __DIR__ . '/routes',
    __DIR__ . '/tests',
  ])
  ->exclude([
    'vendor',
    'storage',
    'bootstrap/cache',
    'public',
  ])
  ->name('*.php')
  ->notName(['*.blade.php']);
```

### Symfony Projects
```php

use PhpCsFixer\Finder;

$finder = (new Finder())
  ->in([
    __DIR__ . '/src',
    __DIR__ . '/config',
    __DIR__ . '/tests',
  ])
  ->exclude([
    'vendor',
    'var',
    'public/bundles',
  ])
  ->name('*.php');
```

## Usage in Your Workflow

```bash
# Apply NCAC PHP-CS-Fixer rules to your project
php-cs-fixer fix

# Or with explicit config reference
php-cs-fixer fix --config=.php-cs-fixer.php
```

## Benefits

- ✅ **Consistent Standards**: Inherits all NCAC quality rules
- ✅ **Project Flexibility**: Customizable file paths and exclusions  
- ✅ **Maintainable**: Automatic updates when NCAC configuration evolves
- ✅ **Performance**: Leverages PHP-CS-Fixer's parallel processing capabilities
