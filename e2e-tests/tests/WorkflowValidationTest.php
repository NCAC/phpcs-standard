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
    return "3-Step Workflow Validation Test";
  }

  public function run(): void {
    $this->step("🚀 Testing complete NCAC 3-step workflow");
    
    // Test each sniff with perfect .fixed files
    $this->testDeclarationSpacingWorkflow();
    $this->testClassOpeningSpacingWorkflow();
    $this->testAlternateSyntaxWorkflow();
    $this->testTwoSpacesIndentWorkflow();
    
    $this->success("All workflow validations passed");
  }

  /**
   * Test DeclarationSpacing with 3-step workflow.
   */
  private function testDeclarationSpacingWorkflow(): void {
    $this->step("Testing DeclarationSpacing 3-step workflow...");
    
    // Create .bad.inc content
    $badContent = '<?php
class   BadSpacing   {
  function    badMethod   (  )  :   void {
    // Multiple spaces everywhere
  }
}';

    // Expected .fixed content (perfectly formatted)
    $expectedFixed = '<?php

declare(strict_types=1);

class BadSpacing {

  function badMethod(): void {
    // Multiple spaces everywhere
  }

}';

    $this->runWorkflowTest('declaration-spacing', $badContent, $expectedFixed);
  }

  /**
   * Test ClassOpeningSpacing with 3-step workflow.
   */
  private function testClassOpeningSpacingWorkflow(): void {
    $this->step("Testing ClassOpeningSpacing 3-step workflow...");
    
    $badContent = '<?php
class BadClass {
  public $property;
}';

    $expectedFixed = '<?php

declare(strict_types=1);

class BadClass {

  public $property;

}';

    $this->runWorkflowTest('class-opening-spacing', $badContent, $expectedFixed);
  }

  /**
   * Test AlternateSyntax with 3-step workflow.
   */
  private function testAlternateSyntaxWorkflow(): void {
    $this->step("Testing AlternateSyntax 3-step workflow...");
    
    $badContent = '<?php
if ($condition):
  echo "test";
endif;

foreach ($items as $item):
  echo $item;
endforeach;';

    $expectedFixed = '<?php

declare(strict_types=1);

if ($condition) {
  echo "test";
}

foreach ($items as $item) {
  echo $item;
}';

    $this->runWorkflowTest('alternate-syntax', $badContent, $expectedFixed);
  }

  /**
   * Test TwoSpacesIndent with 3-step workflow.
   */
  private function testTwoSpacesIndentWorkflow(): void {
    $this->step("Testing TwoSpacesIndent 3-step workflow...");
    
    $badContent = '<?php
class BadIndent {
    public function badMethod() {
        if ($condition) {
            echo "bad indent";
        }
    }
}';

    $expectedFixed = '<?php

declare(strict_types=1);

class BadIndent {

  public function badMethod() {
    if ($condition) {
      echo "bad indent";
    }
  }

}';

    $this->runWorkflowTest('two-spaces-indent', $badContent, $expectedFixed);
  }

  /**
   * Execute the 3-step workflow and validate results.
   */
  private function runWorkflowTest(string $testName, string $badContent, string $expectedFixed): void {
    // Create test file
    $testFile = $this->runner->createTestFile("{$testName}-test.php", $badContent);
    
    // Step 1: PHP-CS-Fixer
    $this->step("  Step 1/3: Running PHP-CS-Fixer...");
    $executable = $this->runner->getWorkingDir() . '/vendor/bin/php-cs-fixer';
    $config = $this->runner->getWorkingDir() . '/.php-cs-fixer.dist.php';
    
    $fixerResult = $this->runner->runCommand(
      "{$executable} fix {$testFile} --config={$config}"
    );
    
    if ($fixerResult['exit_code'] !== 0) {
      throw new \Exception("PHP-CS-Fixer failed for {$testName}: " . $fixerResult['output']);
    }
    
    // Step 2: PHPCBF
    $this->step("  Step 2/3: Running PHPCBF...");
    $phpcbfResult = $this->runner->runPhpcbf($testFile);
    // PHPCBF exit code can be 1 even when successful (if fixes were made)
    
    // Step 3: PHPCS validation (must be clean)
    $this->step("  Step 3/3: Running PHPCS validation...");
    $phpcsResult = $this->runner->runPhpcs($testFile);
    
    if ($phpcsResult['exit_code'] !== 0) {
      throw new \Exception("PHPCS validation failed for {$testName}. Remaining violations:\n" . $phpcsResult['output']);
    }
    
    // Verify result matches expected .fixed content
    $actualResult = file_get_contents($testFile);
    $actualResult = $this->normalizeWhitespace($actualResult);
    $expectedNormalized = $this->normalizeWhitespace($expectedFixed);
    
    if ($actualResult !== $expectedNormalized) {
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
