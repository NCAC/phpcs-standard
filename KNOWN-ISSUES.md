# KNOWN ISSUES

This file documents known issues in the NCAC PHPCS standard that require fixing.

## 1. Undetected Errors in Anonymous Classes (MethodNameSniff)

**Observed Issue:** The sniff fails to detect method naming convention violations in anonymous classes.

```php
// Anonymous class with improper method naming
$anonymous = new class () {

  public function invalid_method() {
    // Method content
  }

  private function internal_helper() {
    // Method content
  }

};
```

**Proposed Solution:** Update `MethodNameSniff.php` to also inspect methods in anonymous classes.

---

## 2. Bug in NCAC.ControlStructures.SwitchDeclaration regarding return statements

The `NCAC.ControlStructures.SwitchDeclaration` sniff requires each `case` and `default` block to end with a `break;` statement, but does not consider the `return` statement as a valid alternative that also terminates the execution flow.

**Observed Issue:** Tests containing `return` statements inside `case` blocks generate errors "Each CASE and DEFAULT must end with a break statement", even though `return` already terminates the execution flow.

**Proposed Solution:** Modify `SwitchDeclarationSniff.php` to consider `return` as equivalent to `break` when validating `case` and `default` blocks.

**Problematic Example:**

```php
switch ($test_file) {
  case 'VariableNameSniffUnitTest.bad.inc':
    return [
      // Results array
    ];
    // Error here because there is no break after the return (although the return makes the break unnecessary)
  break;
}
```

---

## 3. Indentation Bug in NCAC.WhiteSpace.TwoSpacesIndent for Arrays Passed as Arguments

The `NCAC.WhiteSpace.TwoSpacesIndent` sniff does not correctly handle indentation of arrays passed as function arguments across multiple lines.

**Observed Issue:** For an array as an argument on multiple lines with parentheses on the same line, the indentation applied by PHPCBF is incorrect:

```php
// Format applied by PHPCBF (incorrect)
$variable = test([
    45,    // 4 spaces indentation (instead of 2)
    60,    // 4 spaces indentation (instead of 2)
    80     // 4 spaces indentation (instead of 2)
  ]);     // 2 spaces indentation (instead of 0)
```

**Expected Format:**

```php
// Expected format (correct)
$variable = test([
  45,     // 2 spaces indentation
  60,     // 2 spaces indentation
  80      // 2 spaces indentation
]);      // 0 spaces indentation
```

**Proposed Solution:** Modify `TwoSpacesIndentSniff.php` to correct the indentation of arrays passed as function arguments.

---

## 4. Indentation bug for comments and all elements in PHP arrays inside switch/case blocks

The `NCAC.WhiteSpace.TwoSpacesIndent` sniff does not correctly handle indentation when a comment is the first element inside a PHP array returned from a `case` block in a `switch` statement, nor for the rest of the array elements. PHPCBF aligns the comment and all array values with the `return` statement instead of indenting them at the correct level.

**Observed Issue:**
When a comment is the first element in an array returned from a `case` block, PHPCBF produces:

```php
switch ($test_file) {
  case 'MethodNameSniffUnitTest.bad.inc':
    return [

    // Invalid snake_case method names
    10 => 1,  // public function my_method() - should be camelCase
    15 => 1,  // public function calculate_total() - should be camelCase
    // ...
    ];
}
```

**Expected Format:**

```php
switch ($test_file) {
  case 'MethodNameSniffUnitTest.bad.inc':
    return [

      // Invalid snake_case method names
      10 => 1,  // public function my_method() - should be camelCase
      15 => 1,  // public function calculate_total() - should be camelCase
      // ...
    ];
}
```

**Proposed Solution:** Update `TwoSpacesIndentSniff.php` so that comments and all array elements are indented at the same level, even when the array is returned from a `case` block in a `switch` statement.
