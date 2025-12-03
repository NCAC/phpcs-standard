<?php

/**
 * Test naming convention detection.
 */

namespace NCAC\E2ETests;

class NamingConventionsTest extends E2ETest {

  public function getName(): string {
    return "Naming Conventions Test";
  }

  public function run(): void {
    $this->step("Testing correct naming conventions...");
    
    $good_content = '<?php

namespace Test;

class GoodClassName {

  public function goodMethodName() {
    $good_variable = "test";
    return $good_variable;
  }

}

function good_function_name() {
  return true;
}
';
    
    $good_file = $this->runner->createTestFile('good-naming.php', $good_content);
    $good_result = $this->runner->runPhpcs($good_file);
    
    if ($good_result['exit_code'] !== 0) {
      $this->info("Unexpected violations found:");
      $this->info($good_result['output']);
      throw new \Exception("Should not find naming errors in correctly named code");
    }
    $this->success("Correct naming conventions pass");
    
    $this->step("Testing incorrect naming conventions...");
    
    $bad_content = '<?php
namespace Test;

class bad_class_name {
  public function BadMethodName() {
    $BadVariable = "test";
    return $BadVariable;
  }
}

function BadFunctionName() {
  return true;
}
';
    
    $bad_file = $this->runner->createTestFile('bad-naming.php', $bad_content);
    $bad_result = $this->runner->runPhpcs($bad_file);
    
    $this->runner->assertTrue(
      $bad_result['exit_code'] > 0,
      "Should find naming convention errors"
    );
    
    // Count violations
    $error_lines = array_filter($bad_result['lines'], function($line) {
      return strpos($line, 'ERROR') !== false || strpos($line, 'WARNING') !== false;
    });
    
    $this->runner->assertTrue(
      count($error_lines) >= 2,
      "Should find multiple naming violations"
    );
    
    $this->success("Naming convention violations detected (" . count($error_lines) . " violations)");
  }
}
