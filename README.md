# NCAC PHPCS Standard

[![Build Status](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml/badge.svg)](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml)
[![Latest Stable Version](https://img.shields.io/packagist/v/ncac/phpcs-standard)](https://packagist.org/packages/ncac/phpcs-standard)
[![Total Downloads](https://img.shields.io/packagist/dt/ncac/phpcs-standard)](https://packagist.org/packages/ncac/phpcs-standard)
[![PHP Version](https://img.shields.io/packagist/php-v/ncac/phpcs-standard)](https://packagist.org/packages/ncac/phpcs-standard)
[![codecov](https://codecov.io/gh/ncac/phpcs-standard/branch/main/graph/badge.svg)](https://codecov.io/gh/ncac/phpcs-standard)
[![License](https://img.shields.io/github/license/ncac/phpcs-standard)](https://github.com/ncac/phpcs-standard/blob/main/LICENSE)

> **An opinionated PHP coding standard focused on type safety and consistency**
>
> NCAC is a comprehensive PHP_CodeSniffer standard that enforces strict typing, consistent naming conventions, and 2-space indentation. Built on top of proven standards like Slevomat, it adds custom rules designed for modern PHP development practices.

## 🎯 Key Features

### **Strict Type Safety**

Enforces explicit type declarations for all parameters, return values, and properties to catch errors early and improve code clarity.

```php
// ❌ Avoided: ambiguous types
function process($data) {
    return $data->value;
}

// ✅ Required: explicit types
function process(DataObject $data): string {
  return $data->value;
}
```

### **2-Space Indentation with Smart Array Handling**

Uses 2-space indentation with context-aware formatting for arrays and function arguments.

```php
// ✅ Function arguments formatting
$result = my_function([
  'clean' => 'readable',
  'consistent' => 'maintainable'
]);

// ✅ Nested arrays maintain hierarchy
$config = [
  'database' => [
    'host' => 'localhost',
    'port' => 3306
  ]
];
```

### **Consistent Naming Conventions**

Enforces context-appropriate naming: `snake_case` for variables and functions, `camelCase` for class properties and methods, `PascalCase` for classes.

```php
// ✅ Consistent naming across contexts
class UserRepository {

  private string $connectionString;  // camelCase property

  public function findUser(int $user_id): User {  // snake_case parameter
    $query_result = $this->executeQuery($user_id);  // snake_case variable
    return $this->mapToUser($query_result);  // camelCase method
  }

}
```

## 🏗️ What Makes NCAC Different?

This is an **opinionated** standard that makes specific choices about code formatting and structure. While these choices work well for many teams, they may not suit every project or preference.

### **Built on Proven Foundations**

- Extends **Slevomat Coding Standard** for type safety and structural rules
- Adds **7 custom sniffs** for specific formatting requirements
- Requires **PHP 8.0+** for modern type safety features (union types, named arguments, etc.)
- Designed for **auto-fixing** to minimize manual formatting work

> **Note:** For PHP 7.4 support, use version `^1.2.0`. See [BREAKING_CHANGES.md](BREAKING_CHANGES.md) for migration details.

### **Opinionated Choices**

- **2-space indentation** instead of the more common 4-space
- **Strict type hints** required everywhere (may be challenging for legacy code)
- **Specific naming conventions** that mix snake_case and camelCase based on context
- **Minimal class spacing** for compact, readable code structure

### **When to Consider NCAC**

✅ **Good fit for:**

- New projects starting fresh
- Teams that prefer strict typing and consistent formatting
- Projects that can adopt 2-space indentation
- Codebases that can enforce type hints everywhere

⚠️ **Consider carefully for:**

- Large legacy codebases (strict typing requirements)
- Teams strongly preferring 4-space indentation
- Projects with existing PSR-12 compliance requirements
- Mixed coding style preferences within the team

## 📊 Quality Assurance

### Continuous Integration (PHP 8.0 - 8.2)

[![Psalm Analysis](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml/badge.svg?branch=main&job=psalm)](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml)
[![PHPCS Analysis](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml/badge.svg?branch=main&job=phpcs)](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml)
[![PHPUnit Tests](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml/badge.svg?branch=main&job=phpunit)](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml)

## ⚡ Quick Start

### Requirements

- **PHP 8.0+** (for PHP 7.4 support, use version `^1.2.0`)
- Composer

### Installation

```bash
composer require --dev ncac/phpcs-standard
```

### Basic Usage

#### Quick Start: One-Line Command

The easiest way to apply the full NCAC formatting workflow:

```bash
# Using the dedicated command (recommended)
vendor/bin/ncac-format src/

# Preview changes without applying them
vendor/bin/ncac-format --dry-run src/

# Format all files in current directory
vendor/bin/ncac-format

# Alternative: Using Composer script
composer format src/

# Alternative: Using bash script directly
bash vendor/ncac/phpcs-standard/scripts/ncac-fix.sh src/
```

This automatically runs the complete 3-step formatting workflow for you (PHP-CS-Fixer → PHPCBF → PHPCS validation).

> 💡 **Pro Tip:** Add a script to your project's `composer.json` for easy access:
> ```json
> {
>   "scripts": {
>     "format": "vendor/bin/ncac-format",
>     "format:dry": "vendor/bin/ncac-format --dry-run"
>   }
> }
> ```
> Then run: `composer format` or `composer format:dry`

#### Manual: Full Workflow (Advanced)

If you prefer running each step manually:

```bash
# Step 1: Apply PHP-CS-Fixer for complex transformations
vendor/bin/php-cs-fixer fix src/ --config=vendor/ncac/phpcs-standard/.php-cs-fixer.dist.php

# Step 2: Apply PHPCBF for NCAC-specific corrections
vendor/bin/phpcbf --standard=NCAC src/

# Step 3: Check remaining violations
vendor/bin/phpcs --standard=NCAC src/
```

> **Why this workflow?** PHP-CS-Fixer handles ~95% of complex formatting (alternate syntax, spacing), while PHPCBF handles NCAC-specific rules. This combination achieves the best conformity to the standard.

#### Alternative: PHPCS/PHPCBF Only

If you prefer to use only PHPCS tools (without PHP-CS-Fixer):

```bash
# Check your code
vendor/bin/phpcs --standard=NCAC src/

# Auto-fix issues (may not fix all violations)
vendor/bin/phpcbf --standard=NCAC src/
```

> **Note:** Using PHPCBF alone may leave some violations unfixed (~60% auto-fix rate vs ~95% with the full workflow). See [docs/WORKFLOW_STRATEGY.md](docs/WORKFLOW_STRATEGY.md) for details.

### Project Configuration

Create a `phpcs.xml` in your project root:

```xml
<?xml version="1.0"?>
<ruleset name="YourProject">
    <description>Your project coding standard</description>
    <rule ref="NCAC"/>

    <!-- Your source directories -->
    <file>src</file>
    <file>tests</file>

    <!-- Optional: Exclude specific rules if needed -->
    <rule ref="NCAC">
        <exclude name="SlevomatCodingStandard.TypeHints.ParameterTypeHint"/>
    </rule>
</ruleset>
```

## 📚 Documentation

- **📖 [Complete Rules Reference](docs/rules-reference.md)** - Detailed examples for all 21 rules
- **⚙️ [Workflow Strategy](docs/WORKFLOW_STRATEGY.md)** - PHP-CS-Fixer + PHPCBF integration and migration plan
- **🔄 [Code Quality Workflow](docs/CODE_QUALITY_WORKFLOW.md)** - Auto-fixing and CI/CD integration
- **🛠️ [Development Setup Guide](docs/dev-container-setup.md)** - VS Code Dev Container setup
- **🤝 [Contributing Guidelines](CONTRIBUTING.md)** - How to contribute to the project

## 🎨 Code Examples

### Before (Inconsistent formatting)

```php
class userRepository
{

    function findUser($userId)
    {
        $queryResult = $this->executeQuery($userId);
        if ($queryResult) {
            return $queryResult;
        }
        return null;
    }

    private $connectionString;
    const CACHE_TTL = 3600;


}
```

### After (NCAC compliant)

```php
class UserRepository {

    private string $connectionString;

    public const CACHE_TTL = 3600;

    public function findUser(int $user_id): ?User {
      $query_result = $this->executeQuery($user_id);
      if ($query_result !== null) {
        return $this->mapToUser($query_result);
      }
      return null;
    }

}
```

## 🔧 Advanced Configuration

### Custom Rulesets

Disable specific rules for legacy codebases:

```xml
<!-- Gradually adopt NCAC -->
<rule ref="NCAC">
    <!-- Disable strict typing for migration period -->
    <exclude name="SlevomatCodingStandard.TypeHints.ParameterTypeHint"/>
    <exclude name="SlevomatCodingStandard.TypeHints.ReturnTypeHint"/>
</rule>
```

Customize Slevomat spacing rules (built-in configurability):

```xml
<!-- Adjust Slevomat spacing to your team preferences -->
<rule ref="SlevomatCodingStandard.Classes.MethodSpacing">
    <properties>
        <property name="minLinesCountBeforeWithComment" value="1"/>
        <property name="maxLinesCountBeforeWithComment" value="1"/>
    </properties>
</rule>

<rule ref="SlevomatCodingStandard.Classes.PropertySpacing">
    <properties>
        <property name="minLinesCountBeforeWithComment" value="1"/>
        <property name="maxLinesCountBeforeWithComment" value="1"/>
    </properties>
</rule>
```

### Drupal Compatibility

Enable support for Drupal hook naming conventions:

```xml
<!-- Enable Drupal hook support -->
<rule ref="NCAC.NamingConventions.FunctionName">
    <properties>
        <!-- Allow double underscores for Drupal hooks -->
        <property name="allowDoubleUnderscore" value="1"/>
        <!-- Allow leading underscores for internal functions -->
        <property name="allowLeadingUnderscore" value="1"/>
    </properties>
</rule>
```

**Why this is needed:** Drupal uses double underscores (`__`) in hook function names to target specific template suggestions. For example:

```php
// ✅ Valid Drupal preprocess hook (with allowDoubleUnderscore enabled)
function mymodule_preprocess_node__homepage(array &$variables): void {
  // Targets node--homepage.html.twig template
}

// ✅ Valid Drupal theme suggestions hook
function mymodule_theme_suggestions_paragraph__alter(array &$suggestions): void {
  $suggestions[] = 'paragraph__custom';
}

// ✅ Valid internal helper function (with allowLeadingUnderscore enabled)
function _mymodule_internal_helper(string $data): string {
  return strtoupper($data);
}
```

**Without these options enabled**, PHPCBF would incorrectly transform:

- `mymodule_preprocess_node__homepage` → `mymodule_preprocess_node_homepage` ❌ (breaks Drupal hooks!)
- `_internal_helper` → `internal_helper` ❌ (loses private function convention)

**Configuration options:**

- **`allowDoubleUnderscore`** (default: `false`): When enabled, allows `__` in function names for Drupal preprocess hooks and theme suggestions
- **`allowLeadingUnderscore`** (default: `false`): When enabled, allows `_` prefix for marking internal/private functions

> **Note:** NCAC custom sniffs (like `NCAC.Formatting.ClassClosingSpacing`, `NCAC.WhiteSpace.TwoSpacesIndent`) are not yet configurable. This is planned for future releases.

## 🛠️ Development & Contributing

### Development Setup

#### VS Code Dev Container (Recommended)

1. **Clone the repository**:

   ```bash
   git clone https://github.com/ncac/phpcs-standard.git
   cd phpcs-standard
   ```

2. **Generate environment** (before opening in VS Code):

   ```bash
   .docker/generate-env.sh
   ```

3. **Open in VS Code**: The Dev Container will automatically configure everything.

#### Manual Setup

```bash
# Install dependencies
composer install
pnpm install

# Run full quality checks
vendor/bin/phing check

# Run specific checks
vendor/bin/psalm              # Static analysis
vendor/bin/phpunit            # Unit tests
vendor/bin/phpcs --standard=NCAC NCAC/  # Self-check
```

## 🔧 Tool Strategy: PHPCS vs PHP-CS-Fixer

NCAC uses a **strategic separation** between detection and correction to avoid conflicts and ensure reliable results:

### Current Implementation (v1.0)

```bash
# Detection and simple fixes
vendor/bin/phpcs --standard=NCAC src/ --fix

# Some complex rules are detection-only (see below)
vendor/bin/phpcs --standard=NCAC src/
```

### Why Some Rules Are Detection-Only

Certain rules like **NoAlternateControlStructureSniff** intentionally provide **no automatic fixes** due to PHP_CodeSniffer's token processing limitations:

- **Token conflicts**: Sequential sniffs can overwrite each other's modifications
- **Invalid syntax**: Complex transformations can generate broken PHP code
- **Execution order**: Later sniffs (like indentation) may undo earlier fixes

**Example issue:**
```php
// Original: if ($x): ... endif;
// After sniff A: if ($x) { ... }  // Fixed
// After sniff B: if ($x) { ...    // Broken (missing closing brace)
```

### Planned Evolution (v4.x)

```bash
# 1. Complex transformations via PHP-CS-Fixer
php-cs-fixer fix --config=.ncac-cs-fixer.php src/

# 2. Simple fixes + validation via PHPCS  
vendor/bin/phpcs --standard=NCAC src/ --fix

# 3. Final validation
vendor/bin/phpcs --standard=NCAC src/
```

> **📖 Learn more:** See [docs/PHPCS_VS_PHPCSFIXER_STRATEGY.md](docs/PHPCS_VS_PHPCSFIXER_STRATEGY.md) for technical details.

### Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for:

- 🐛 **Bug reports** with reproducible examples
- ✨ **Feature requests** with clear use cases
- 🔧 **Pull requests** with comprehensive tests
- 📖 **Documentation** improvements

## 🏆 Acknowledgments

NCAC is built upon two excellent foundational projects:

- **[PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)** by Squiz Labs - provides the tokenization engine and auto-fixing capabilities
- **[Slevomat Coding Standard](https://github.com/slevomat/coding-standard)** - provides many of the type safety and structural rules

NCAC adds **7 custom sniffs** for specific formatting and naming requirements:

- 2-space indentation with context-aware array handling
- Mixed naming conventions (snake_case variables, camelCase properties, PascalCase classes)
- Minimal class spacing and structure rules
- Modern PHP pattern enforcement

> **License Compliance:** NCAC operates under the MIT license and respects all incorporated project licenses. See [LICENSE](LICENSE) for details.

## 🚀 Requirements & Compatibility

- **PHP:** 7.4, 8.0, 8.1, 8.2, 8.3
- **PHP_CodeSniffer:** 3.7.0 or higher
- **Composer:** 2.0 or higher

## ✨ Features At a Glance

- ✅ **21 comprehensive rules** covering all aspects of PHP code quality
- ✅ **Auto-fixable formatting** for seamless workflow integration
- ✅ **Type safety enforcement** with mandatory type hints
- ✅ **Modern PHP support** including enums and typed properties
- ✅ **IDE integration** for VS Code, PhpStorm, and others
- ✅ **Performance optimized** for large codebases
- ✅ **Extensive test coverage** ensuring reliability
- ✅ **Rich documentation** with practical examples

## 📞 Support & Community

- 📚 **[Documentation Hub](docs/)** - Comprehensive guides and references
- 🐛 **[Issue Tracker](https://github.com/ncac/phpcs-standard/issues)** - Bug reports and feature requests
- 💬 **[Discussions](https://github.com/ncac/phpcs-standard/discussions)** - Community Q&A and ideas
- 📊 **[Releases](https://github.com/ncac/phpcs-standard/releases)** - Version history and changelogs

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

**Get started:**

```bash
composer require --dev ncac/phpcs-standard
vendor/bin/phpcs --standard=NCAC src/
```
