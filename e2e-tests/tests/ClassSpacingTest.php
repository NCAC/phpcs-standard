<?php

/**
 * Test class opening/closing spacing detection.
 * 
 * This test specifically covers the regression bug where classes with
 * implements clauses and comments after the opening brace were incorrectly
 * flagged as having invalid spacing.
 */

namespace NCAC\E2ETests;

class ClassSpacingTest extends E2ETest {

  public function getName(): string {
    return "Class Spacing Test";
  }

  public function run(): void {
    // Test 1: Class with implements and comment (regression test)
    $this->step("Testing class with implements and comment after brace...");

    $content = '<?php

namespace Test;

interface TestInterface {

  public function test(): void;

}

class GoodClass implements TestInterface {

  // This comment should not affect blank line detection
  public function test(): void {
    return;
  }

}
';

    $file = $this->runner->createTestFile('class-spacing-implements.php', $content);
    $this->success("Test file created");

    $result = $this->runner->runPhpcs($file);

    // Should NOT find ClassOpeningSpacing errors
    $has_spacing_error = false;
    foreach ($result['lines'] as $line) {
      if (strpos($line, 'blank line(s) after the class opening brace') !== false) {
        $has_spacing_error = true;
        break;
      }
    }

    $this->runner->assertFalse(
      $has_spacing_error,
      "Class with implements and comment should NOT trigger spacing error"
    );

    $this->success("No false positive spacing errors detected");

    // Test 2: Class without implements (baseline test)
    $this->step("Testing simple class with comment after brace...");

    $content2 = '<?php

namespace Test;

class SimpleClass {

  // Comment after blank line
  private $value = 1;

}
';

    $file2 = $this->runner->createTestFile('class-spacing-simple.php', $content2);
    $result2 = $this->runner->runPhpcs($file2);

    $has_spacing_error2 = false;
    foreach ($result2['lines'] as $line) {
      if (strpos($line, 'blank line(s) after the class opening brace') !== false) {
        $has_spacing_error2 = true;
        break;
      }
    }

    $this->runner->assertFalse(
      $has_spacing_error2,
      "Simple class with comment should NOT trigger spacing error"
    );

    $this->success("Simple class spacing check passed");

    // Test 3: Class with INCORRECT spacing (should detect error)
    $this->step("Testing class with INCORRECT spacing...");

    $content3 = '<?php

namespace Test;

class BadClass {
  // No blank line after opening brace - should trigger error
  private $value = 1;

}
';

    $file3 = $this->runner->createTestFile('class-spacing-bad.php', $content3);
    $result3 = $this->runner->runPhpcs($file3);

    $has_spacing_error3 = false;
    foreach ($result3['lines'] as $line) {
      if (strpos($line, 'blank line(s) after the class opening brace') !== false) {
        $has_spacing_error3 = true;
        break;
      }
    }

    $this->runner->assertTrue(
      $has_spacing_error3,
      "Class without blank line should trigger spacing error"
    );

    $this->success("Correctly detected missing blank line");

    // Test 4: Verify PHPCBF can fix the spacing issue
    $this->step("Testing auto-fix for spacing issue...");

    $result4 = $this->runner->runPhpcbf($file3);

    $this->runner->assertTrue(
      $result4['exit_code'] === 0 || strpos($result4['output'], 'FIXED') !== false,
      "PHPCBF should successfully fix spacing issues"
    );

    // Verify the fix worked
    $result5 = $this->runner->runPhpcs($file3);

    $has_spacing_error5 = false;
    foreach ($result5['lines'] as $line) {
      if (strpos($line, 'blank line(s) after the class opening brace') !== false) {
        $has_spacing_error5 = true;
        break;
      }
    }

    $this->runner->assertFalse(
      $has_spacing_error5,
      "After PHPCBF fix, spacing error should be resolved"
    );

    $this->success("PHPCBF successfully fixed spacing issue");
  }
}
