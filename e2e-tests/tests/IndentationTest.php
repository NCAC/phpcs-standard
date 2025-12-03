<?php

/**
 * Test indentation detection and fixing.
 */

namespace NCAC\E2ETests;

class IndentationTest extends E2ETest {

  public function getName(): string {
    return "Indentation Test";
  }

  public function run(): void {
    $this->step("Testing correct indentation...");
    
    $good_content = '<?php

namespace Test;

class TestClass {

  public function testMethod() {
    if (true) {
      return "correct";
    }
  }

}
';
    
    $good_file = $this->runner->createTestFile('good-indent.php', $good_content);
    $good_result = $this->runner->runPhpcs($good_file);
    
    if ($good_result['exit_code'] !== 0) {
      $this->info("Unexpected violations found:");
      $this->info($good_result['output']);
      throw new \Exception("Should not find indentation errors in correctly indented code");
    }
    $this->success("Correctly indented code passes");
    
    $this->step("Testing incorrect indentation...");
    
    $bad_content = '<?php
namespace Test;

class TestClass {
public function testMethod() {
if (true) {
return "incorrect";
}
}
}
';
    
    $bad_file = $this->runner->createTestFile('bad-indent.php', $bad_content);
    $bad_result = $this->runner->runPhpcs($bad_file);
    
    $this->runner->assertTrue(
      $bad_result['exit_code'] > 0,
      "Should find indentation errors"
    );
    
    $this->runner->assertContains(
      'indented incorrectly',
      $bad_result['output'],
      "Should report indentation errors"
    );
    
    $this->success("Incorrect indentation detected");
    
    $this->step("Testing auto-fix for indentation...");
    
    $this->runner->runPhpcbf($bad_file);
    $fixed_result = $this->runner->runPhpcs($bad_file);
    
    $errors_before = substr_count($bad_result['output'], 'ERROR');
    $errors_after = substr_count($fixed_result['output'], 'ERROR');
    
    $this->runner->assertTrue(
      $errors_after < $errors_before,
      "Auto-fix should reduce indentation errors"
    );
    
    $this->success("Indentation auto-fixed successfully");
  }
}
