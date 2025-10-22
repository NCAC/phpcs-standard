# NCAC PHPCS Standard - Rules Reference

This document provides a detailed description of each rule in the NCAC PHPCS Standard with examples and configuration options.

## Rules Overview

The NCAC standard includes **21 rules** combining industry standards with NCAC-specific conventions for modern, type-safe PHP development.

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
Enforces snake_case for variable names and function parameters; and camelCase for class properties.

**BAD:**

```php
$myVariable = 1;
$My_variable = 2;

class MyClass {
  public $my_property;
}

function foo($myParam) {}
```

**GOOD:**

```php
$my_variable = 1;

class MyClass {
  public $myProperty;
}

function foo($my_param) {}
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
Enforces exactly two spaces for indentation (no tabs, no 4-spaces). Supports TypeScript-style indentation for arrays in function arguments.

**BAD:**

```php
function foo() {
    echo 'bar'; // 4 spaces
}

$result = my_function([
    'key' => 'value'  // Inconsistent indentation
]);
```

**GOOD:**

```php
function foo() {
  echo 'bar'; // 2 spaces
}

// TypeScript-style indentation for function arguments
$result = my_function([
  'key' => 'value'
]);

// Regular indentation for standalone arrays
$array = [
  'key' => 'value'
];
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

## Rule Categories

### **Generic/Core PHP Rules (2 rules):**

- `Generic.NamingConventions.UpperCaseConstantName` - UPPER_CASE constants
- `Generic.PHP.LowerCaseConstant` - lowercase PHP magic constants

### **Slevomat Rules - Type Safety & Structure (10 rules):**

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

### **Squiz Rules - Control Flow & Formatting (2 rules):**

- `Squiz.ControlStructures.ControlSignature` - control structure formatting
- `Squiz.WhiteSpace.OperatorSpacing` - operator spacing

### **NCAC Custom Rules (7 rules):**

- `NCAC.ControlStructures.SwitchDeclaration` - strict switch statement rules
- `NCAC.Formatting.ClassClosingSpacing` - class closing brace spacing
- `NCAC.Formatting.ClassOpeningSpacing` - class opening brace spacing
- `NCAC.Formatting.NoAlternateControlStructure` - forbid alternate syntax
- `NCAC.Formatting.OpeningBraceKAndR` - K&R brace style
- `NCAC.NamingConventions.PascalCaseClassName` - PascalCase classes
- `NCAC.NamingConventions.MethodName` - camelCase methods
- `NCAC.NamingConventions.VariableName` - context-aware variable naming
- `NCAC.NamingConventions.FunctionName` - snake_case functions
- `NCAC.WhiteSpace.TwoSpacesIndent` - 2-space indentation with TypeScript-style features

## Configuration Examples

### Custom phpcs.xml with rule customization:

```xml
<?xml version="1.0"?>
<ruleset name="YourProject">
    <description>Your project coding standard</description>
    <rule ref="NCAC"/>

    <!-- Configure class spacing -->
    <rule ref="NCAC.Formatting.ClassClosingSpacing">
        <properties>
            <property name="linesCount" value="2"/>
        </properties>
    </rule>

    <!-- Configure method spacing -->
    <rule ref="SlevomatCodingStandard.Classes.MethodSpacing">
        <properties>
            <property name="minLinesCountBeforeWithComment" value="1"/>
            <property name="maxLinesCountBeforeWithComment" value="1"/>
        </properties>
    </rule>

    <!-- Exclude specific rules if needed -->
    <rule ref="NCAC">
        <exclude name="NCAC.NamingConventions.VariableName"/>
    </rule>
</ruleset>
```

---

**Back to:** [Main Documentation](../README.md)
