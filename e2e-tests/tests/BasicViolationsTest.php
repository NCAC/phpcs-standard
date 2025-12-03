<?php

/**
 * Test basic violation detection.
 */

namespace NCAC\E2ETests;

class BasicViolationsTest extends E2ETest {

  public function getName(): string {
    return "Basic Violations Detection Test";
  }

  public function run(): void {
    $this->step("Creating a file with intentional violations...");
    
    $content = '<?php
// Test file with multiple violations
namespace Test;

class   test_class     {
public function    bad_method_name( $bad_var_name ) {
$bad_indentation = "test";
        return $bad_indentation;
}
}
';
    
    $file = $this->runner->createTestFile('basic-violations.php', $content);
    $this->success("Test file created");
    
    $this->step("Running PHPCS to detect violations...");
    
    $result = $this->runner->runPhpcs($file);
    
    // Should find errors
    $this->runner->assertTrue(
      $result['exit_code'] > 0,
      "Expected PHPCS to find violations (exit code should be > 0)"
    );
    
    // Count errors
    $error_count = 0;
    foreach ($result['lines'] as $line) {
      if (strpos($line, 'ERROR') !== false || strpos($line, 'WARNING') !== false) {
        $error_count++;
      }
    }
    
    $this->runner->assertTrue(
      $error_count > 0,
      "Expected to find at least one violation"
    );
    
    $this->success("Detected {$error_count} violation lines");
    
    // Check specific violations
    $this->step("Verifying specific violations are detected...");
    
    // Should detect indentation issues
    $this->runner->assertContains(
      'indented incorrectly',
      $result['output'],
      "Should detect indentation issues"
    );
    
    $this->success("All expected violations detected");
  }
}
