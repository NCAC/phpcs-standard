<?php

declare(strict_types=1);

namespace NCAC\E2ETests;

/**
 * E2E Test for DeclarationSpacingSniff
 *
 * Tests that multiple spaces in declarations are detected and fixed.
 */
class DeclarationSpacingTest extends E2ETest {

  public function getName(): string {
    return "Declaration Spacing Test";
  }

  public function run(): void {
    $this->testDetectsMultipleSpacesInFunctions();
    $this->testDetectsMultipleSpacesInClasses();
    $this->testDetectsMultipleSpacesInInterfaces();
    $this->testDetectsMultipleSpacesInTraits();
    $this->testFixesMultipleSpaces();
    $this->testAcceptsSingleSpaces();
  }

  /**
   * Test that multiple spaces are detected in function declarations.
   */
  private function testDetectsMultipleSpacesInFunctions(): void {
    $this->step("Testing multiple spaces in function declarations...");

    $code = <<<'PHP'
<?php

function   my_function(): void {
  echo "test";
}

function  another_function(): string {
  return "test";
}
PHP;

    $file = $this->runner->createTestFile('test-function-spacing.php', $code);
    $result = $this->runner->runPhpcs($file, ['sniffs' => 'NCAC.WhiteSpace.DeclarationSpacing']);

    // Check for errors line by line (PHPCS may wrap long messages)
    $found_function_3_spaces = false;
    $found_function_2_spaces = false;

    foreach ($result['lines'] as $line) {
      if (strpos($line, 'FUNCTION keyword') !== false && strpos($line, '3 found') !== false) {
        $found_function_3_spaces = true;
      }
      if (strpos($line, 'FUNCTION keyword') !== false && strpos($line, '2 found') !== false) {
        $found_function_2_spaces = true;
      }
    }

    $this->runner->assertTrue(
      $found_function_3_spaces,
      "Should detect 3 spaces after FUNCTION keyword"
    );

    $this->runner->assertTrue(
      $found_function_2_spaces,
      "Should detect 2 spaces after FUNCTION keyword"
    );

    $this->success("Correctly detected multiple spaces in function declarations");
  }

  /**
   * Test that multiple spaces are detected in class declarations.
   */
  private function testDetectsMultipleSpacesInClasses(): void {
    $this->step("Testing multiple spaces in class declarations...");

    $code = <<<'PHP'
<?php

class    MyClass {

  public function    myMethod(): void {
    echo "test";
  }

}
PHP;

    $file = $this->runner->createTestFile('test-class-spacing.php', $code);
    $result = $this->runner->runPhpcs($file, ['sniffs' => 'NCAC.WhiteSpace.DeclarationSpacing']);

    // Check for errors line by line
    $found_class_error = false;
    $found_method_error = false;

    foreach ($result['lines'] as $line) {
      if (strpos($line, 'CLASS keyword') !== false && strpos($line, '4 found') !== false) {
        $found_class_error = true;
      }
      if (strpos($line, 'FUNCTION keyword') !== false && strpos($line, '4 found') !== false) {
        $found_method_error = true;
      }
    }

    $this->runner->assertTrue(
      $found_class_error,
      "Should detect 4 spaces after CLASS keyword"
    );

    $this->runner->assertTrue(
      $found_method_error,
      "Should detect 4 spaces after FUNCTION keyword"
    );

    $this->success("Correctly detected multiple spaces in class declarations");
  }

  /**
   * Test that multiple spaces are detected in interface declarations.
   */
  private function testDetectsMultipleSpacesInInterfaces(): void {
    $this->step("Testing multiple spaces in interface declarations...");

    $code = <<<'PHP'
<?php

interface   MyInterface {

  public function doSomething(): void;

}
PHP;

    $file = $this->runner->createTestFile('test-interface-spacing.php', $code);
    $result = $this->runner->runPhpcs($file, ['sniffs' => 'NCAC.WhiteSpace.DeclarationSpacing']);

    // Check for errors line by line
    $found_interface_error = false;

    foreach ($result['lines'] as $line) {
      if (strpos($line, 'INTERFACE keyword') !== false && strpos($line, '3 found') !== false) {
        $found_interface_error = true;
      }
    }

    $this->runner->assertTrue(
      $found_interface_error,
      "Should detect 3 spaces after INTERFACE keyword"
    );

    $this->success("Correctly detected multiple spaces in interface declarations");
  }

  /**
   * Test that multiple spaces are detected in trait declarations.
   */
  private function testDetectsMultipleSpacesInTraits(): void {
    $this->step("Testing multiple spaces in trait declarations...");

    $code = <<<'PHP'
<?php

trait  MyTrait {

  public function traitMethod(): void {
    echo "test";
  }

}
PHP;

    $file = $this->runner->createTestFile('test-trait-spacing.php', $code);
    $result = $this->runner->runPhpcs($file, ['sniffs' => 'NCAC.WhiteSpace.DeclarationSpacing']);

    // Check for errors line by line
    $found_trait_error = false;

    foreach ($result['lines'] as $line) {
      if (strpos($line, 'TRAIT keyword') !== false && strpos($line, '2 found') !== false) {
        $found_trait_error = true;
      }
    }

    $this->runner->assertTrue(
      $found_trait_error,
      "Should detect 2 spaces after TRAIT keyword"
    );

    $this->success("Correctly detected multiple spaces in trait declarations");
  }

  /**
   * Test that PHPCBF fixes multiple spaces correctly.
   */
  private function testFixesMultipleSpaces(): void {
    $this->step("Testing PHPCBF fixes multiple spaces...");

    $code = <<<'PHP'
<?php

function   my_function(): void {
  echo "test";
}

class    MyClass {

  public function    myMethod(): void {
    echo "test";
  }

}
PHP;

    $expected_fixed = <<<'PHP'
<?php

function my_function(): void {
  echo "test";
}

class MyClass {

  public function myMethod(): void {
    echo "test";
  }

}
PHP;

    $file = $this->runner->createTestFile('test-fix-spacing.php', $code);
    $this->runner->runPhpcbf($file, ['sniffs' => 'NCAC.WhiteSpace.DeclarationSpacing']);
    $fixed_content = file_get_contents($file);

    $this->runner->assertEquals(
      $expected_fixed,
      $fixed_content,
      "PHPCBF should fix all spacing issues"
    );

    $this->success("PHPCBF correctly fixed all spacing issues");
  }

  /**
   * Test that single spaces are not flagged as errors.
   */
  private function testAcceptsSingleSpaces(): void {
    $this->step("Testing single spaces are accepted...");

    $code = <<<'PHP'
<?php

function my_function(): void {
  echo "test";
}

class MyClass {

  public function myMethod(): void {
    echo "test";
  }

}

interface MyInterface {

  public function doSomething(): void;

}

trait MyTrait {

  public function traitMethod(): void {
    echo "test";
  }

}
PHP;

    $file = $this->runner->createTestFile('test-correct-spacing.php', $code);
    $result = $this->runner->runPhpcs($file, ['sniffs' => 'NCAC.WhiteSpace.DeclarationSpacing']);

    // Should have NO errors related to spacing
    $found_spacing_error = false;

    foreach ($result['lines'] as $line) {
      if (strpos($line, 'Expected 1 space after') !== false) {
        $found_spacing_error = true;
      }
    }

    $this->runner->assertFalse(
      $found_spacing_error,
      "Should not find spacing errors in correctly formatted code"
    );

    $this->success("Correctly accepted single spaces");
  }
}
