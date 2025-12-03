<?php

/**
 * Test closure indentation (recently fixed issue).
 */

namespace NCAC\E2ETests;

class ClosureIndentationTest extends E2ETest {

  public function getName(): string {
    return "Closure Indentation Test";
  }

  public function run(): void {
    $this->step("Testing correct closure indentation...");
    
    $good_content = '<?php
$result = preg_replace_callback(
  \'/pattern/\',
  function ($matches) {
    return strtoupper($matches[1]);
  },
  $input
);

$mapped = array_map(
  function ($item) {
    return $item * 2;
  },
  $array
);
';
    
    $good_file = $this->runner->createTestFile('good-closure.php', $good_content);
    $good_result = $this->runner->runPhpcs($good_file);
    
    $this->runner->assertEquals(
      0,
      $good_result['exit_code'],
      "Should not find errors in correctly indented closures"
    );
    $this->success("Correctly indented closures pass");
    
    $this->step("Testing incorrect closure indentation...");
    
    $bad_content = '<?php
$result = preg_replace_callback(
  \'/pattern/\',
  function ($matches) {
return strtoupper($matches[1]);
  },
  $input
);

$mapped = array_map(
  function ($item) {
      return $item * 2;
  },
  $array
);
';
    
    $bad_file = $this->runner->createTestFile('bad-closure.php', $bad_content);
    $bad_result = $this->runner->runPhpcs($bad_file);
    
    $this->runner->assertTrue(
      $bad_result['exit_code'] > 0,
      "Should find indentation errors in closures"
    );
    
    $this->runner->assertContains(
      'indented incorrectly',
      $bad_result['output'],
      "Should report closure indentation errors"
    );
    
    $this->success("Closure indentation errors detected");
    
    $this->step("Testing arrow function indentation...");
    
    $arrow_content = '<?php
$mapper = fn($item) => [
  \'id\' => $item->id,
  \'name\' => $item->name
];

$results = array_map(
  fn($user) => [
    \'id\' => $user->getId(),
    \'email\' => $user->getEmail()
  ],
  $users
);
';
    
    $arrow_file = $this->runner->createTestFile('arrow-function.php', $arrow_content);
    $arrow_result = $this->runner->runPhpcs($arrow_file);
    
    $this->runner->assertEquals(
      0,
      $arrow_result['exit_code'],
      "Should not find errors in correctly indented arrow functions"
    );
    
    $this->success("Arrow function indentation works correctly");
  }
}
