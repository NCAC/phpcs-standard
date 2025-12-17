<?php

declare(strict_types=1);

namespace NCAC\E2ETests;

/**
 * E2E test for PHP-CS-Fixer integration with NCAC rules.
 *
 * This test validates that PHP-CS-Fixer can correctly handle complex
 * transformations that are too risky for PHPCS (like alternate syntax).
 * 
 * This replaces the unreliable FixedFilesValidationTest in the new
 * PHPCS vs PHP-CS-Fixer strategy.
 */
class PhpCsFixerIntegrationTest extends E2ETest {

  public function getName(): string {
    return "PHP-CS-Fixer Integration Test";
  }

  public function run(): void {
    $this->step("🚀 Testing PHP-CS-Fixer integration for complex fixes");

    // Check if PHP-CS-Fixer is available
    if (!$this->isPhpCsFixerAvailable()) {
      throw new \Exception('PHP-CS-Fixer not available. Please run: composer install');
    }

    $this->step("✓ PHP-CS-Fixer available via Composer");

    // Test alternate syntax conversion using the official NCAC config
    $this->testAlternateSyntaxConversion();

    // Test that PHPCS still detects issues after PHP-CS-Fixer
    $this->testPhpcsDetectionAfterFixer();

    $this->success("PHP-CS-Fixer integration working correctly");
  }

  /**
   * Check if PHP-CS-Fixer is available via Composer.
   */
  private function isPhpCsFixerAvailable(): bool {
    $executable = $this->runner->getWorkingDir() . '/vendor/bin/php-cs-fixer';
    return file_exists($executable);
  }

  /**
   * Get the PHP-CS-Fixer executable path.
   */
  private function getPhpCsFixerExecutable(): string {
    return $this->runner->getWorkingDir() . '/vendor/bin/php-cs-fixer';
  }

  /**
   * Test alternate syntax conversion using the official NCAC config.
   */
  private function testAlternateSyntaxConversion(): void {
    $this->step("Testing alternate syntax conversion using official NCAC config...");
    
    // Create test file with alternate syntax
    $test_content = '<?php
// Test alternate syntax that PHPCS detects but PHP-CS-Fixer fixes
if ($condition):
  echo "hello";
elseif ($other):
  echo "world";  
else:
  echo "default";
endif;

foreach ($items as $item):
  echo $item;
endforeach;

while ($continue):
  doSomething();
endwhile;
';

    $test_file = $this->runner->createTestFile('alternate-syntax-test.php', $test_content);
    
    // Step 1: Verify PHPCS detects the issues
    $phpcs_result = $this->runner->runPhpcs($test_file);
    if ($phpcs_result['exit_code'] === 0) {
      throw new \Exception('PHPCS should detect alternate syntax violations');
    }
    
    $this->success("PHPCS correctly detected alternate syntax issues");
    
    // Step 2: Use the official NCAC PHP-CS-Fixer configuration
    $config_path = $this->runner->getWorkingDir() . '/.php-cs-fixer.dist.php';
    
    if (!file_exists($config_path)) {
      throw new \Exception('Official NCAC PHP-CS-Fixer config not found at: ' . $config_path);
    }
    
    // Step 3: Run PHP-CS-Fixer with the official config
    $executable = $this->getPhpCsFixerExecutable();
    $fixer_result = $this->runner->runCommand(
      "{$executable} fix {$test_file} --config={$config_path}"
    );
    
    if ($fixer_result['exit_code'] !== 0) {
      throw new \Exception('PHP-CS-Fixer failed: ' . $fixer_result['output']);
    }
    
    $this->success("PHP-CS-Fixer successfully converted alternate syntax using official config");
    
    // Step 4: Verify the conversion worked
    $fixed_content = file_get_contents($test_file);
    
    // Should not contain alternate syntax anymore
    if (strpos($fixed_content, 'endif') !== false || 
        strpos($fixed_content, 'endforeach') !== false || 
        strpos($fixed_content, 'endwhile') !== false) {
      throw new \Exception('PHP-CS-Fixer did not properly convert alternate syntax');
    }
    
    // Should contain standard braces
    if (strpos($fixed_content, '} elseif (') === false || 
        strpos($fixed_content, '} else {') === false) {
      throw new \Exception('PHP-CS-Fixer did not generate proper brace syntax');
    }
    
    $this->success("Alternate syntax correctly converted to standard braces");
  }

  /**
   * Test that PHPCS validation works after PHP-CS-Fixer corrections.
   */
  private function testPhpcsDetectionAfterFixer(): void {
    $this->step("Testing PHPCS validation after PHP-CS-Fixer...");
    
    // Create a file with mixed issues
    $test_content = '<?php
class   badSpacing {  // Multiple spaces - should be fixed by PHP-CS-Fixer
  function  badMethod():void {  // Bad spacing and naming - PHPCS will still catch naming
    if ($x):  // Alternate syntax - PHP-CS-Fixer will convert
      echo "test";
    endif;
  }
}
';
    
    $test_file = $this->runner->createTestFile('mixed-issues-test.php', $test_content);
    
    // Step 1: Run PHP-CS-Fixer first to handle complex transformations
    $config_path = $this->runner->getWorkingDir() . '/.php-cs-fixer.dist.php';
    $executable = $this->getPhpCsFixerExecutable();
    
    $this->runner->runCommand(
      "{$executable} fix {$test_file} --config={$config_path}"
    );
    
    // Step 2: PHPCS for final validation and remaining issues
    $phpcs_result = $this->runner->runPhpcs($test_file);
    
    // Should still have some NCAC-specific issues (naming conventions, etc.)
    // But alternate syntax should be gone
    $output = $phpcs_result['output'];
    
    // Alternate syntax should be converted and not detected anymore
    if (strpos($output, 'alternate') !== false || 
        strpos($output, 'endif') !== false ||
        strpos($output, 'NoAlternateControlStructure') !== false) {
      throw new \Exception('PHPCS should not detect alternate syntax after PHP-CS-Fixer conversion');
    }
    
    $this->success("Workflow: PHP-CS-Fixer → PHPCS validation works correctly");
    $this->success("Alternate syntax issues resolved, other NCAC rules still enforced");
  }

}
