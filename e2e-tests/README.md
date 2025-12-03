# E2E Tests for NCAC PHP_CodeSniffer Standard

This directory contains end-to-end tests that verify the NCAC standard works correctly in real-world scenarios.

## Running Tests

Run all E2E tests:

```bash
php e2e-tests/run.php
```

Or use the composer script:

```bash
composer run e2e-test
```

## Test Structure

- `E2ETestRunner.php` - Main test runner that orchestrates all tests
- `E2ETest.php` - Base class for all E2E tests
- `tests/` - Individual test classes

## Available Tests

### 1. StandardLoadTest
Verifies that PHPCS can load the NCAC standard and that all expected sniffs are registered.

### 2. BasicViolationsTest
Tests that the standard can detect basic code violations.

### 3. AutoFixTest
Tests that PHPCBF can automatically fix violations.

### 4. IndentationTest
Comprehensive tests for indentation detection and fixing.

### 5. NamingConventionsTest
Tests for naming convention rules (classes, methods, functions, variables).

### 6. ClosureIndentationTest
Tests for correct closure and arrow function indentation (including the recent bugfix).

### 7. MethodChainingTest
Tests for method chaining indentation rules.

### 8. SelfComplianceTest
Verifies that the NCAC codebase itself passes its own coding standard (dogfooding).

## Adding New Tests

To add a new E2E test:

1. Create a new class in `tests/` that extends `E2ETest`
2. Implement `getName()` and `run()` methods
3. Add the class to the `discoverTests()` method in `E2ETestRunner.php`

Example:

```php
<?php

namespace NCAC\E2ETests;

class MyNewTest extends E2ETest {

  public function getName(): string {
    return "My New Test";
  }

  public function run(): void {
    $this->step("Testing something...");
    
    $content = '<?php /* test code */';
    $file = $this->runner->createTestFile('test.php', $content);
    $result = $this->runner->runPhpcs($file);
    
    $this->runner->assertEquals(0, $result['exit_code']);
    $this->success("Test passed!");
  }
}
```

## Helper Methods

The `E2ETestRunner` provides helper methods for tests:

- `createTestFile($name, $content)` - Create a temporary test file
- `runPhpcs($file, $options)` - Run PHPCS on a file
- `runPhpcbf($file, $options)` - Run PHPCBF on a file
- `assertContains($needle, $haystack, $message)` - Assert string contains substring
- `assertEquals($expected, $actual, $message)` - Assert equality
- `assertTrue($condition, $message)` - Assert true
- `assertFalse($condition, $message)` - Assert false

The `E2ETest` base class provides output methods:

- `step($message)` - Print a step message
- `success($message)` - Print a success message
- `info($message)` - Print an info message

## Continuous Integration

These tests are designed to run in CI environments and will:

- Exit with code 0 if all tests pass
- Exit with code 1 if any test fails
- Automatically clean up temporary files

## Test Output

The test runner provides a detailed summary:

```
╔══════════════════════════════════════════════════════════════════════════════╗
║ Test Summary                                                                 ║
╠══════════════════════════════════════════════════════════════════════════════╣
║  Total:    8 tests                                                           ║
║  Passed:   8 tests ✅                                                        ║
║  Failed:   0 tests ❌                                                        ║
║  Success Rate: 100.0%                                                        ║
╚══════════════════════════════════════════════════════════════════════════════╝

🎉 All E2E tests passed!
📦 The NCAC PHP_CodeSniffer standard is ready for production use.
```
