# NCAC PHPCS Standard

[![Latest Stable Version](https://img.shields.io/packagist/v/ncac/phpcs-standard.svg?style=flat-square)](https://packagist.org/packages/ncac/phpcs-standard)
[![Total Downloads](https://img.shields.io/packagist/dt/ncac/phpcs-standard.svg?style=flat-square)](https://packagist.org/packages/ncac/phpcs-standard)

> **Philosophy:** The NCAC standard brings a "TypeScript-like" approach to PHP code: strict, explicit, readable, and modern. It encourages best practices, clear naming, and formatting conventions inspired by the TypeScript/ESLint ecosystem, but adapted for PHP.
>
> **Acknowledgement:** The NCAC standard is heavily inspired by the [Slevomat Coding Standard](https://github.com/slevomat/coding-standard), which provides many of the strict type, documentation, and structure rules that NCAC builds upon. We thank the Slevomat team and contributors for their high-quality open source work. Parts of this standard may directly use or adapt rules and logic from Slevomat, in accordance with its [license](https://github.com/slevomat/coding-standard/blob/master/LICENSE).

## Installation

```bash
composer require --dev ncac/phpcs-standard
```

## Usage

### Basic usage with phpcs:

```bash
vendor/bin/phpcs --standard=NCAC /path/to/your/code
```

### Basic usage with phpcbf (auto-fix):

```bash
vendor/bin/phpcbf --standard=NCAC /path/to/your/code
```

### Configuration in phpcs.xml:

```xml
<?xml version="1.0"?>
<ruleset name="YourProject">
    <description>Your project coding standard</description>
    <rule ref="NCAC"/>

    <!-- Optional: Configure specific rules -->
    <rule ref="NCAC.Formatting.ClassClosingSpacing">
        <properties>
            <property name="linesCount" value="2"/>
        </properties>
    </rule>
</ruleset>
```

## Rules Overview

Below is a detailed description of each rule in the `ruleset.xml` with its purpose and BAD/GOOD code examples.

---

## Generic.NamingConventions.UpperCaseConstantName

**What it does:**
Enforces that all constants are named in uppercase with underscores (UPPER_CASE).

**BAD:**

```php
const myConstant = 1;
const My_Constant = 2;
```

**GOOD:**

```php
const MY_CONSTANT = 1;
```

---

## SlevomatCodingStandard.Namespaces.AlphabeticallySortedUses

**What it does:**
Requires that all `use` statements in a namespace are sorted alphabetically.

**BAD:**

```php
use B\ClassB;
use A\ClassA;
```

**GOOD:**

```php
use A\ClassA;
use B\ClassB;
```

---

## Generic.PHP.LowerCaseConstant

**What it does:**
Ensures that PHP magic constants (like `true`, `false`, `null`) are written in lowercase.

**BAD:**

```php
$bool = TRUE;
$value = NULL;
$check = FALSE;
```

**GOOD:**

```php
$bool = true;
$value = null;
$check = false;
```

---

## SlevomatCodingStandard.Classes.ClassConstantVisibility

**What it does:**
Requires that all class constants have an explicit visibility (public, protected, private).

**BAD:**

```php
class MyClass {
  const FOO = 1;
}
```

**GOOD:**

```php
class MyClass {
  public const FOO = 1;
}
```

---

## Squiz.ControlStructures.ControlSignature

**What it does:**
Enforces correct spacing and formatting for control structures (if, for, while, etc.).

**BAD:**

```php
if($a==1){
    // ...
}
```

**GOOD:**

```php
if ($a == 1) {
    // ...
}
```

---

## Squiz.WhiteSpace.OperatorSpacing

**What it does:**
Requires a single space around operators (e.g., `=`, `+`, `-`, etc.).

**BAD:**

```php
$a=1+2;
```

**GOOD:**

```php
$a = 1 + 2;
```

---

## SlevomatCodingStandard.Classes.ClassMemberSpacing

**What it does:**
Enforces a specific number of blank lines between class members (properties, methods).

**BAD:**

```php
class MyClass {
  public $a;
  public $b;
}
```

**GOOD:**

```php
class MyClass {
  public $a;

  public $b;
}
```

---

## SlevomatCodingStandard.Classes.MethodSpacing

**What it does:**
Enforces a specific number of blank lines before and after methods.

**BAD:**

```php
class MyClass {
  public function foo() {}
  public function bar() {}
}
```

**GOOD:**

```php
class MyClass {
  public function foo() {}

  public function bar() {}
}
```

---

## SlevomatCodingStandard.Classes.ClassStructure

**What it does:**
Enforces a specific order for class elements (constants, properties, methods, etc.).

**BAD:**

```php
class MyClass {
  private $b;
  public function foo() {}
  public $a;
}
```

**GOOD:**

```php
class MyClass {
  public $a;
  private $b;

  public function foo() {}
}
```

---

## SlevomatCodingStandard.Classes.PropertySpacing

**What it does:**
Enforces blank lines before properties, with or without comments.

**BAD:**

```php
class MyClass {
  /**
   * @var int
   */
  public $a;
  public $b;
}
```

**GOOD:**

```php
class MyClass {
  /**
   * @var int
   */
  public $a;

  public $b;
}
```

---

## SlevomatCodingStandard.Classes.BackedEnumTypeSpacing

**What it does:**
Enforces spacing rules for backed enum types (PHP 8.1+).

**BAD:**

```php
enum Status: int{OK = 1;}
```

**GOOD:**

```php
enum Status : int { OK = 1; }
```

---

## NCAC.Formatting.ClassClosingSpacing

**What it does:**
Enforces blank lines before the closing brace of a class (NCAC standard).

**BAD:**

```php
class MyClass {
  public $a;
}
```

**GOOD:**

```php
class MyClass {
  public $a;

}
```

---

## NCAC.Formatting.ClassOpeningSpacing

**What it does:**
Enforces blank lines after the opening brace of a class (NCAC standard).

**BAD:**

```php
class MyClass {
  public $a;
}
```

**GOOD:**

```php
class MyClass {

  public $a;
}
```

---

## NCAC.ControlStructures.SwitchDeclaration

**What it does:**
Enforces strict formatting and structure for SWITCH statements, including mandatory `break` statements.

**BAD:**

```php
switch ($a) {
  case 1:
    echo 'one';
  case 2:
    echo 'two';
    break;
}
```

**GOOD:**

```php
switch ($a) {
  case 1:
    echo 'one';
    break;
  case 2:
    echo 'two';
    break;
  default:
    break;
}
```

---

## NCAC.Formatting.NoAlternateControlStructure

**What it does:**
Forbids alternate control structure syntax (e.g., `if: ... endif;`). Only curly braces `{}` are allowed.

**BAD:**

```php
if ($a):
    echo $a;
endif;
```

**GOOD:**

```php
if ($a) {
    echo $a;
}
```

---

## NCAC.Formatting.OpeningBraceKAndR

**What it does:**
Enforces K&R style for opening braces: the opening brace must be on the same line as the declaration.

**BAD:**

```php
class MyClass
{
  // ...
}
```

**GOOD:**

```php
class MyClass {
  // ...
}
```

---

## NCAC.NamingConventions.PascalCaseClassName

**What it does:**
Enforces PascalCase for class, interface, and trait names.

**BAD:**

```php
class my_class {}
class myClass {}
```

**GOOD:**

```php
class MyClass {}
```

---

## NCAC.NamingConventions.MethodName

**What it does:**
Enforces camelCase for method names (except magic methods).

**BAD:**

```php
class MyClass {
  public function my_method() {}
}
```

**GOOD:**

```php
class MyClass {
  public function myMethod() {}
}
```

---

## NCAC.NamingConventions.VariableName

**What it does:**
Enforces snake_case for variable names and function parameters ; and camelCase for class properties.

**BAD:**

```php
$myVariable = 1;
$My_variable = 2;

class MyClass {
  public $my_property;
}

function ($myParam) {}
```

**GOOD:**

```php
$my_variable = 1;

class MyClass {
  public $myProperty;
}

function ($my_param) {}

```

---

## NCAC.NamingConventions.FunctionName

**What it does:**
Enforces snake_case for function names (outside classes/traits).

**BAD:**

```php
function myFunction() {}
```

**GOOD:**

```php
function my_function() {}
```

---

## NCAC.WhiteSpace.TwoSpacesIndent

**What it does:**
Enforces exactly two spaces for indentation (no tabs, no 4-spaces).

**BAD:**

```php
function foo() {
    echo 'bar'; // 4 spaces
}
```

**GOOD:**

```php
function foo() {
  echo 'bar'; // 2 spaces
}
```

---

## SlevomatCodingStandard.TypeHints.ParameterTypeHint

**What it does:**
Requires all function and method parameters to have a type hint (except for cases where it's not possible, e.g. variadic or mixed).

**BAD:**

```php
function foo($bar) {}
```

**GOOD:**

```php
function foo(string $bar) {}
```

---

## SlevomatCodingStandard.TypeHints.ReturnTypeHint

**What it does:**
Requires all functions and methods to declare a return type hint (except for constructors, destructors, etc.).

**BAD:**

```php
function foo($bar) {
  return (string)$bar;
}
```

**GOOD:**

```php
function foo($bar): string {
  return (string)$bar;
}
```

---

## SlevomatCodingStandard.TypeHints.PropertyTypeHint

**What it does:**
Requires all class properties to have a type hint (PHP 7.4+ typed properties).

**BAD:**

```php
class MyClass {
  public $foo;
}
```

**GOOD:**

```php
class MyClass {
  public string $foo;
}
```

---

## Rule Summary

This NCAC standard includes:

### **Generic/Core PHP Rules:**

- `Generic.NamingConventions.UpperCaseConstantName` - UPPER_CASE constants
- `Generic.PHP.LowerCaseConstant` - lowercase PHP magic constants

### **Slevomat Rules (Type Safety & Structure):**

- `SlevomatCodingStandard.Namespaces.AlphabeticallySortedUses` - sorted imports
- `SlevomatCodingStandard.Classes.ClassConstantVisibility` - explicit constant visibility
- `SlevomatCodingStandard.Classes.ClassMemberSpacing` - member spacing
- `SlevomatCodingStandard.Classes.MethodSpacing` - method spacing
- `SlevomatCodingStandard.Classes.ClassStructure` - class element ordering
- `SlevomatCodingStandard.Classes.PropertySpacing` - property spacing
- `SlevomatCodingStandard.Classes.BackedEnumTypeSpacing` - enum spacing
- `SlevomatCodingStandard.TypeHints.ParameterTypeHint` - parameter types
- `SlevomatCodingStandard.TypeHints.ReturnTypeHint` - return types
- `SlevomatCodingStandard.TypeHints.PropertyTypeHint` - property types

### **Squiz Rules (Control Flow & Formatting):**

- `Squiz.ControlStructures.ControlSignature` - control structure formatting
- `Squiz.WhiteSpace.OperatorSpacing` - operator spacing

### **NCAC Custom Rules:**

- `NCAC.ControlStructures.SwitchDeclaration` - strict switch statement rules
- `NCAC.Formatting.ClassClosingSpacing` - class closing brace spacing
- `NCAC.Formatting.ClassOpeningSpacing` - class opening brace spacing
- `NCAC.Formatting.NoAlternateControlStructure` - forbid alternate syntax
- `NCAC.Formatting.OpeningBraceKAndR` - K&R brace style
- `NCAC.NamingConventions.PascalCaseClassName` - PascalCase classes
- `NCAC.NamingConventions.MethodName` - camelCase methods
- `NCAC.NamingConventions.VariableName` - context-aware variable naming
- `NCAC.NamingConventions.FunctionName` - snake_case functions
- `NCAC.WhiteSpace.TwoSpacesIndent` - 2-space indentation

**Total: 21 rules** combining industry standards with NCAC-specific conventions for modern, type-safe PHP development.

## Requirements

- PHP 7.4 or higher
- PHP_CodeSniffer 3.7.0 or higher
- Composer

## Features

- ✅ **Auto-fixable rules:** Most rules support automatic fixing with `phpcbf`
- ✅ **Type safety:** Enforces type hints for parameters, returns, and properties
- ✅ **Modern PHP:** Supports PHP 8.0+ features including enums and typed properties
- ✅ **Configurable:** Many rules can be customized via properties
- ✅ **Performance:** Optimized sniffs with minimal performance impact
- ✅ **IDE Integration:** Works seamlessly with VS Code, PhpStorm, and other IDEs

## Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

- 📚 [Documentation](README.md)
- 🐛 [Issue Tracker](https://github.com/ncac/phpcs-standard/issues)
- 💬 [Discussions](https://github.com/ncac/phpcs-standard/discussions)
