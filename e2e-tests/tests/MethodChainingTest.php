<?php

/**
 * Test method chaining indentation.
 */

namespace NCAC\E2ETests;

class MethodChainingTest extends E2ETest {

  public function getName(): string {
    return "Method Chaining Test";
  }

  public function run(): void {
    $this->step("Testing correct method chaining indentation...");
    
    $good_content = <<<'PHP'
<?php

class TestClass {

  public function testMethod() {
    $result = $this->queryBuilder
      ->select("*")
      ->from("users")
      ->where("active = 1")
      ->orderBy("created_at DESC")
      ->limit(10)
      ->getResult();

    return $result;
  }

}

PHP;
    
    $good_file = $this->runner->createTestFile('good-chaining.php', $good_content);
    $good_result = $this->runner->runPhpcs($good_file);
    
    if ($good_result['exit_code'] !== 0) {
      $this->info("Unexpected violations found:");
      $this->info($good_result['output']);
      throw new \Exception("Should not find errors in correctly chained methods");
    }
    $this->success("Correctly chained methods pass");
    
    $this->step("Testing incorrect method chaining indentation...");
    
    $bad_content = <<<'PHP'
<?php
$result = $this->queryBuilder
->select("*")
    ->from("users")
->where("active = 1")
  ->orderBy("created_at DESC")
->limit(10)
  ->getResult();

PHP;
    
    $bad_file = $this->runner->createTestFile('bad-chaining.php', $bad_content);
    $bad_result = $this->runner->runPhpcs($bad_file);
    
    $this->runner->assertTrue(
      $bad_result['exit_code'] > 0,
      "Should find indentation errors in method chaining"
    );
    
    $this->runner->assertContains(
      'indented incorrectly',
      $bad_result['output'],
      "Should report method chaining indentation errors"
    );
    
    $error_count = substr_count($bad_result['output'], 'ERROR');
    $this->runner->assertTrue(
      $error_count >= 3,
      "Should find multiple method chaining errors"
    );
    
    $this->success("Method chaining errors detected ({$error_count} errors)");
  }
}
