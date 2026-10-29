<?php

declare(strict_types=1);

namespace NCAC\E2ETests;

/**
 * New E2E test architecture with 3-step workflow validation.
 *
 * This test validates the complete NCAC workflow:
 * 1. PHP-CS-Fixer (complex transformations)
 * 2. PHPCBF (simple PHPCS fixes)
 * 3. PHPCS (final validation)
 */
class WorkflowValidationTest extends E2ETest {

  public function getName(): string {
    return '3-Step Workflow Validation Test';
  }

  public function run(): void {
    $this->step('🚀 Testing complete NCAC 3-step workflow');
    // Test each sniff with perfect .fixed files
    $this->testDeclarationSpacingWorkflow();
    $this->testClassOpeningSpacingWorkflow();
    $this->testAlternateSyntaxWorkflow();
    $this->testTwoSpacesIndentWorkflow();
    $this->success('All workflow validations passed');
  }

  /**
   * Test DeclarationSpacing with 3-step workflow.
   */
  private function testDeclarationSpacingWorkflow(): void {
    $this->step('Testing DeclarationSpacing 3-step workflow...');
    // Create .bad.inc content
    $bad_content = <<<'CODE'
<?php
class   BadSpacing   {
  function    badMethod   (  )  :   void {
    // Multiple spaces everywhere
  }
}
CODE;

    // Expected .fixed content (perfectly formatted)
    $expected_fixed = <<<'CODE'
<?php

declare(strict_types=1);

class BadSpacing {

  function badMethod(): void {
    // Multiple spaces everywhere
  }

}
CODE;

    $this->runWorkflowTest('declaration-spacing', $bad_content, $expected_fixed);
  }

  /**
   * Test ClassOpeningSpacing with 3-step workflow.
   */
  private function testClassOpeningSpacingWorkflow(): void {
    $this->step('Testing ClassOpeningSpacing 3-step workflow...');
    
    $bad_content = <<<'CODE'
<?php
class BadClass {
  public $property;
}
CODE;

    $expected_fixed = <<<'CODE'
<?php

declare(strict_types=1);

class BadClass {

  public $property;

}
CODE;

    $this->runWorkflowTest('class-opening-spacing', $bad_content, $expected_fixed);
  }

  /**
   * Test AlternateSyntax with 3-step workflow.
   */
  private function testAlternateSyntaxWorkflow(): void {
    $this->step('Testing AlternateSyntax 3-step workflow...');
    
    $bad_content = <<<'CODE'
<?php
if ($condition):
  echo "test";
endif;

foreach ($items as $item):
  echo $item;
endforeach;
CODE;

    $expected_fixed = <<<'CODE'
<?php

declare(strict_types=1);

if ($condition) {
  echo "test";
}

foreach ($items as $item) {
  echo $item;
}
CODE;

    $this->runWorkflowTest('alternate-syntax', $bad_content, $expected_fixed);
  }

  /**
   * Test TwoSpacesIndent with 3-step workflow.
   */
  private function testTwoSpacesIndentWorkflow(): void {
    $this->step('Testing TwoSpacesIndent 3-step workflow...');
    
    $bad_content = <<<'CODE'
<?php
class BadIndent {
    public function badMethod() {
        if ($condition) {
            echo "bad indent";
        }
    }
}
CODE;

    $expected_fixed = <<<'CODE'
<?php

declare(strict_types=1);

class BadIndent {

  public function badMethod() {
    if ($condition) {
      echo "bad indent";
    }
  }

}
CODE;

    $this->runWorkflowTest('two-spaces-indent', $bad_content, $expected_fixed);
  }

  /**
   * Execute the 3-step workflow and validate results.
   */
  private function runWorkflowTest(string $test_name, string $bad_content, string $expected_fixed): void {
    // Create test file
    $test_file = $this->runner->createTestFile("{$testName}-test.php", $bad_content);
    // Step 1: PHP-CS-Fixer
    $this->step('  Step 1/3: Running PHP-CS-Fixer...');
    $executable = $this->runner->getWorkingDir() . '/vendor/bin/php-cs-fixer';
    $config = $this->runner->getWorkingDir() . '/.php-cs-fixer.dist.php';
    $fixer_result = $this->runner->runCommand(
      "{$executable} fix {$testFile} --config={$config}"
    );
    if ($fixer_result['exit_code'] !== 0) {
      throw new \Exception("PHP-CS-Fixer failed for {$testName}: " . $fixer_result['output']);
    }
    // Step 2: PHPCBF
    $this->step('  Step 2/3: Running PHPCBF...');
    $phpcbf_result = $this->runner->runPhpcbf($test_file);
    // PHPCBF exit code can be 1 even when successful (if fixes were made)
    // Step 3: PHPCS validation (must be clean)
    $this->step('  Step 3/3: Running PHPCS validation...');
    $phpcs_result = $this->runner->runPhpcs($test_file);
    if ($phpcs_result['exit_code'] !== 0) {
      throw new \Exception("PHPCS validation failed for {$testName}. Remaining violations:\n" . $phpcs_result['output']);
    }
    // Verify result matches expected .fixed content
    $actual_result = file_get_contents($test_file);
    $actual_result = $this->normalizeWhitespace($actual_result);
    $expected_normalized = $this->normalizeWhitespace($expected_fixed);
    if ($actual_result !== $expected_normalized) {
      throw new \Exception("Workflow result for {$testName} doesn't match expected .fixed content.\n" .
      "Expected:\n{$expectedNormalized}\n\n" .
      "Actual:\n{$actualResult}");
    }
    $this->success("  ✓ {$testName} workflow validation passed");
  }

  /**
   * Normalize whitespace for comparison.
   */
  private function normalizeWhitespace(string $content): string {
    // Remove trailing whitespace from each line
    $lines = explode("\n", $content);
    $lines = array_map('rtrim', $lines);
    return implode("\n", $lines);
  }

}
