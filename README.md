# NCAC PHPCS Standard

[![Build Status](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml/badge.svg)](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml)
[![Latest Stable Version](https://img.shields.io/packagist/v/ncac/phpcs-standard)](https://packagist.org/packages/ncac/phpcs-standard)
[![Total Downloads](https://img.shields.io/packagist/dt/ncac/phpcs-standard)](https://packagist.org/packages/ncac/phpcs-standard)
[![PHP Version](https://img.shields.io/packagist/php-v/ncac/phpcs-standard)](https://packagist.org/packages/ncac/phpcs-standard)
[![codecov](https://codecov.io/gh/ncac/phpcs-standard/branch/main/graph/badge.svg)](https://codecov.io/gh/ncac/phpcs-standard)
[![License](https://img.shields.io/github/license/ncac/phpcs-standard)](https://github.com/ncac/phpcs-standard/blob/main/LICENSE)

> **Modern PHP, TypeScript Philosophy, Maximum Confidence**
>
> Transform your PHP development with a coding standard that brings the best of TypeScript/ESLint ecosystem to PHP. NCAC enforces strict typing, explicit patterns, and consistent formatting for code that reads like poetry and runs with confidence.

## 🎯 Why NCAC?

### **Type-First Development**

Every parameter, return value, and property must be explicitly typed. No more guessing, no more runtime surprises.

```php
// ❌ Old way: ambiguous and error-prone
function process($data) {
    return $data->value;
}

// ✅ NCAC way: crystal clear intent
function process(DataObject $data): string {
  return $data->value;
}
```

### **Intelligent Indentation**

Revolutionary 2-space indentation with TypeScript-inspired array handling. Function arguments get special treatment for maximum readability.

```php
// ✅ TypeScript-style function arguments
$result = my_function([
  'clean' => 'readable',
  'consistent' => 'beautiful'
]);

// ✅ Regular arrays maintain full indentation hierarchy
$config = [
  'database' => [
    'host' => 'localhost',
    'port' => 3306
  ]
];
```

### **Context-Aware Naming**

Smart naming conventions that adapt to context: `snake_case` for variables and functions, `camelCase` for class properties and methods, `PascalCase` for classes.

```php
// ✅ Context-aware naming in action
class UserRepository {

  private string $connectionString;  // camelCase property

  public function findUser(int $user_id): User {  // snake_case parameter
    $query_result = $this->executeQuery($user_id);  // snake_case variable
    return $this->mapToUser($query_result);  // camelCase method
  }

}
```

## 🚀 Philosophy: The TypeScript of PHP

The NCAC standard reimagines PHP development through the lens of modern TypeScript practices:

### **1. Explicit is Better Than Implicit**

- **Mandatory type hints** for parameters, returns, and properties
- **Explicit visibility** for all class constants and members
- **Clear intent** through naming conventions that tell a story

### **2. Consistency Breeds Confidence**

- **21 carefully curated rules** that work together harmoniously
- **Auto-fixable formatting** means your code always looks professional
- **IDE-first design** for seamless development experience

### **3. Modern PHP, Maximum Leverage**

- **PHP 7.4+ features** like typed properties and arrow functions
- **Enum support** for PHP 8.1+ with proper spacing
- **Performance optimized** sniffs that don't slow you down

### **4. Developer Experience First**

- **Fail fast** with immediate feedback on type and style issues
- **Smart defaults** that rarely need customization
- **Comprehensive documentation** with real-world examples

## 📊 Quality Assurance

### Continuous Integration (PHP 7.4 - 8.2)

[![Psalm Analysis](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml/badge.svg?branch=main&job=psalm)](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml)
[![PHPCS Analysis](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml/badge.svg?branch=main&job=phpcs)](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml)
[![PHPUnit Tests](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml/badge.svg?branch=main&job=phpunit)](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml)

## ⚡ Quick Start

### Installation

```bash
composer require --dev ncac/phpcs-standard
```

### Basic Usage

```bash
# Check your code
vendor/bin/phpcs --standard=NCAC src/

# Auto-fix issues
vendor/bin/phpcbf --standard=NCAC src/
```

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
- **🛠️ [Development Setup Guide](docs/dev-container-setup.md)** - VS Code Dev Container setup
- **🤝 [Contributing Guidelines](CONTRIBUTING.md)** - How to contribute to the project
- **📋 [Known Issues](KNOWN-ISSUES.md)** - Current limitations and workarounds

## 🏗️ What Makes NCAC Different?

### **Built for Modern PHP**

- **PHP 7.4+ typed properties** fully supported
- **PHP 8.0+ enums** with proper spacing rules
- **Auto-fixable rules** for seamless workflow integration
- **Performance optimized** for large codebases

### **Developer Experience Focus**

- **IDE-first design** works seamlessly with VS Code, PhpStorm
- **Comprehensive test coverage** ensures reliability
- **Rich error messages** guide you to the solution
- **Minimal configuration** required

### **TypeScript-Inspired Intelligence**

- **Smart indentation** adapts to context (function args vs. standalone arrays)
- **Consistent naming** that scales across teams and projects
- **Explicit typing** catches errors before they reach production
- **Modern formatting** that's easy to read and maintain

## 🎨 Code Examples

### Before NCAC (Inconsistent, Unclear)

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

### After NCAC (Crystal Clear, Type-Safe)

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

## 🌐 Philosophy: Technology Convergence

### **The Future is Typed and Unified**

Modern software development is converging towards **explicit typing**, **consistent patterns**, and **predictable behavior** across all languages and platforms:

- **TypeScript** transformed JavaScript from chaos to confidence
- **Swift** brought type safety to mobile development
- **Rust** proved that safety and performance can coexist
- **PHP 7.4+** embraced typed properties, union types, and strict typing

NCAC recognizes this **historical trend** and positions PHP as a first-class citizen in the modern development ecosystem. We're not just writing PHP code—we're writing **future-proof**, **maintainable**, and **trustworthy** software that scales with your business.

### **Why This Matters**

- **Team Velocity:** Developers familiar with TypeScript/ESLint can instantly read NCAC-compliant PHP
- **Career Growth:** Skills transfer seamlessly between languages
- **Code Quality:** Consistent patterns reduce cognitive load and bugs
- **Tooling Integration:** Modern IDEs and static analyzers work better with explicit types

> _"The languages that survive and thrive are those that embrace clarity over cleverness, explicitness over magic, and safety over shortcuts."_

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

### Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for:

- 🐛 **Bug reports** with reproducible examples
- ✨ **Feature requests** with clear use cases
- 🔧 **Pull requests** with comprehensive tests
- 📖 **Documentation** improvements

## 🏆 Recognition & Acknowledgments

### **Standing on the Shoulders of Giants**

The NCAC standard is built upon and deeply grateful to two foundational projects:

#### **PHP_CodeSniffer Foundation**

This project would not exist without [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) by Squiz Labs. PHP_CodeSniffer provides the robust tokenization engine, extensible architecture, and auto-fixing capabilities that make NCAC possible. We extend our heartfelt thanks to the maintainers and contributors of this essential PHP tool.

#### **Slevomat Coding Standard Excellence**

The NCAC standard incorporates and builds upon the excellent [Slevomat Coding Standard](https://github.com/slevomat/coding-standard), which provides many of the strict typing, documentation, and structural rules that define modern PHP development. The quality and comprehensiveness of Slevomat's work has been instrumental in shaping NCAC's approach to type safety and code organization.

### **NCAC's Custom Contribution**

While leveraging these proven foundations, NCAC adds **7 custom sniffs** that implement:

- **Revolutionary 2-space indentation** with TypeScript-style array handling
- **Context-aware naming conventions** (snake_case variables, camelCase properties, PascalCase classes)
- **Strict formatting rules** for class structure and control flow
- **Modern PHP patterns** aligned with contemporary development practices

All custom rules are designed to complement (not replace) the existing ecosystem, ensuring maximum compatibility and adoption.

> **License Compliance:** NCAC respects and operates under the licenses of all incorporated projects. See our [LICENSE](LICENSE) file for complete details.

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

**Ready to transform your PHP code?** 🚀

```bash
composer require --dev ncac/phpcs-standard
vendor/bin/phpcs --standard=NCAC src/
```
