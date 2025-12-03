<?php

/**
 * Test automatic fixing with PHPCBF.
 */

namespace NCAC\E2ETests;

class AutoFixTest extends E2ETest {

  public function getName(): string {
    return "Auto-Fix Test (PHPCBF)";
  }

  public function run(): void {
    $this->step("Creating a file with fixable violations...");
    
    $content = '<?php
namespace Test;

class TestClass {
  public function testMethod() {
      return "bad indentation";
  }
}
';
    
    $file = $this->runner->createTestFile('auto-fix.php', $content);
    $this->success("Test file created");
    
    $this->step("Verifying violations exist before fix...");
    
    $before = $this->runner->runPhpcs($file);
    $this->runner->assertTrue(
      $before['exit_code'] > 0,
      "Expected violations before auto-fix"
    );
    $this->success("Violations detected");
    
    $this->step("Running PHPCBF to auto-fix violations...");
    
    $fix_result = $this->runner->runPhpcbf($file);
    $this->success("PHPCBF completed");
    
    $this->step("Verifying violations are fixed...");
    
    $after = $this->runner->runPhpcs($file);
    
    // Check if file content was changed
    $fixed_content = file_get_contents($file);
    $this->runner->assertTrue(
      $fixed_content !== $content,
      "File should be modified after auto-fix"
    );
    
    // Should have fewer errors (ideally none for fixable issues)
    $before_errors = substr_count($before['output'], 'ERROR');
    $after_errors = substr_count($after['output'], 'ERROR');
    
    $this->runner->assertTrue(
      $after_errors < $before_errors,
      "Expected fewer errors after auto-fix (before: {$before_errors}, after: {$after_errors})"
    );
    
    $this->success("Violations reduced from {$before_errors} to {$after_errors}");
  }
}
