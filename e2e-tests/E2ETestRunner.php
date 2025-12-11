<?php

/**
 * End-to-end test runner for the NCAC PHP_CodeSniffer Standard.
 *
 * This runner orchestrates all E2E tests to ensure the standard works correctly
 * in real-world scenarios.
 *
 * @author   NCAC
 * @category PHP_CodeSniffer
 * @package  NCAC
 * @license  https://github.com/NCAC/phpcs-standard/blob/main/LICENSE MIT License
 */

namespace NCAC\E2ETests;

class E2ETestRunner {

  private string $working_dir;
  private string $phpcs_executable;
  private string $phpcbf_executable;
  private string $standard = 'NCAC';
  private int $passed = 0;
  private int $failed = 0;
  private array $test_files = [];

  public function __construct(string $working_dir) {
    $this->working_dir = $working_dir;
    $this->phpcs_executable = $working_dir . '/vendor/bin/phpcs';
    $this->phpcbf_executable = $working_dir . '/vendor/bin/phpcbf';
  }

  /**
   * Run all E2E tests.
   */
  public function run(): int {
    $this->printHeader();

    if (!$this->checkPrerequisites()) {
      return 1;
    }

    // Load all test classes
    $test_classes = $this->discoverTests();

    foreach ($test_classes as $test_class) {
      $test = new $test_class($this);
      $this->runTest($test);
    }

    $this->printSummary();
    $this->cleanup();

    return $this->failed > 0 ? 1 : 0;
  }

  /**
   * Discover all test classes.
   */
  private function discoverTests(): array {
    return [
      StandardLoadTest::class,
      BasicViolationsTest::class,
      AutoFixTest::class,
      IndentationTest::class,
      NamingConventionsTest::class,
      ClosureIndentationTest::class,
      MethodChainingTest::class,
      ClassSpacingTest::class,
      DrupalHooksTest::class,
      SelfComplianceTest::class,
    ];
  }

  /**
   * Run a single test.
   */
  private function runTest(E2ETest $test): void {
    echo "\n" . str_repeat('─', 80) . "\n";
    echo "Running: " . $test->getName() . "\n";
    echo str_repeat('─', 80) . "\n\n";

    try {
      $test->run();
      $this->passed++;
      echo "✅ PASSED\n";
    } catch (\Exception $e) {
      $this->failed++;
      echo "❌ FAILED: " . $e->getMessage() . "\n";
      if ($e->getTrace()) {
        echo "   " . $e->getFile() . ":" . $e->getLine() . "\n";
      }
    }
  }

  /**
   * Check prerequisites.
   */
  private function checkPrerequisites(): bool {
    if (!file_exists($this->phpcs_executable)) {
      echo "❌ ERROR: PHPCS executable not found at: {$this->phpcs_executable}\n";
      echo "   Please run: composer install\n";
      return false;
    }

    if (!file_exists($this->phpcbf_executable)) {
      echo "❌ ERROR: PHPCBF executable not found at: {$this->phpcbf_executable}\n";
      echo "   Please run: composer install\n";
      return false;
    }

    return true;
  }

  /**
   * Print header.
   */
  private function printHeader(): void {
    echo "\n";
    echo "╔" . str_repeat('═', 78) . "╗\n";
    echo "║" . str_pad(" NCAC PHP_CodeSniffer Standard - E2E Tests", 78) . "║\n";
    echo "╚" . str_repeat('═', 78) . "╝\n";
  }

  /**
   * Print summary.
   */
  private function printSummary(): void {
    $total = $this->passed + $this->failed;
    $percentage = $total > 0 ? round(($this->passed / $total) * 100, 1) : 0;

    echo "\n";
    echo "╔" . str_repeat('═', 78) . "╗\n";
    echo "║" . str_pad(" Test Summary", 78) . "║\n";
    echo "╠" . str_repeat('═', 78) . "╣\n";
    echo "║  Total:  " . str_pad($total, 3, ' ', STR_PAD_LEFT) . " tests" . str_repeat(' ', 61) . "║\n";
    echo "║  Passed: " . str_pad($this->passed, 3, ' ', STR_PAD_LEFT) . " tests ✅" . str_repeat(' ', 58) . "║\n";
    echo "║  Failed: " . str_pad($this->failed, 3, ' ', STR_PAD_LEFT) . " tests ❌" . str_repeat(' ', 58) . "║\n";
    echo "║  Success Rate: {$percentage}%" . str_repeat(' ', 61 - strlen($percentage)) . "║\n";
    echo "╚" . str_repeat('═', 78) . "╝\n\n";

    if ($this->failed === 0) {
      echo "🎉 All E2E tests passed!\n";
      echo "📦 The NCAC PHP_CodeSniffer standard is ready for production use.\n\n";
    } else {
      echo "⚠️  Some tests failed. Please review the output above.\n\n";
    }
  }

  /**
   * Clean up test files.
   */
  private function cleanup(): void {
    foreach ($this->test_files as $file) {
      if (file_exists($file)) {
        unlink($file);
      }
    }
  }

  // Public helper methods for tests

  public function getPhpcsExecutable(): string {
    return $this->phpcs_executable;
  }

  public function getPhpcbfExecutable(): string {
    return $this->phpcbf_executable;
  }

  public function getStandard(): string {
    return $this->standard;
  }

  public function getWorkingDir(): string {
    return $this->working_dir;
  }

  public function createTestFile(string $name, string $content): string {
    $file = $this->working_dir . '/e2e-tests/tmp/' . $name;
    $dir = dirname($file);

    if (!is_dir($dir)) {
      mkdir($dir, 0755, true);
    }

    file_put_contents($file, $content);
    $this->test_files[] = $file;

    return $file;
  }

  public function runPhpcs(string $file, array $options = []): array {
    $cmd = escapeshellarg($this->phpcs_executable);
    $cmd .= ' --standard=' . escapeshellarg($this->standard);

    // Exclude Slevomat return type hints for E2E tests (too strict for simple examples)
    $cmd .= ' --exclude=SlevomatCodingStandard.TypeHints.ReturnTypeHint,SlevomatCodingStandard.TypeHints.ParameterTypeHint';


    foreach ($options as $key => $value) {
      if ($value === true) {
        $cmd .= ' --' . $key;
      } else {
        $cmd .= ' --' . $key . '=' . escapeshellarg($value);
      }
    }

    $cmd .= ' ' . escapeshellarg($file) . ' 2>&1';

    exec($cmd, $output, $exit_code);

    return [
      'output' => implode("\n", $output),
      'exit_code' => $exit_code,
      'lines' => $output,
    ];
  }

  public function runPhpcbf(string $file, array $options = []): array {
    $cmd = escapeshellarg($this->phpcbf_executable);
    $cmd .= ' --standard=' . escapeshellarg($this->standard);

    // Exclude Slevomat return type hints for E2E tests (can't auto-fix)
    $cmd .= ' --exclude=SlevomatCodingStandard.TypeHints.ReturnTypeHint,SlevomatCodingStandard.TypeHints.ParameterTypeHint';


    foreach ($options as $key => $value) {
      if ($value === true) {
        $cmd .= ' --' . $key;
      } else {
        $cmd .= ' --' . $key . '=' . escapeshellarg($value);
      }
    }

    $cmd .= ' ' . escapeshellarg($file) . ' 2>&1';

    exec($cmd, $output, $exit_code);

    return [
      'output' => implode("\n", $output),
      'exit_code' => $exit_code,
      'lines' => $output,
    ];
  }

  public function assertContains(string $needle, string $haystack, string $message = ''): void {
    if (strpos($haystack, $needle) === false) {
      throw new \Exception($message ?: "Expected string not found: '{$needle}'");
    }
  }

  public function assertNotContains(string $needle, string $haystack, string $message = ''): void {
    if (strpos($haystack, $needle) !== false) {
      throw new \Exception($message ?: "Unexpected string found: '{$needle}'");
    }
  }

  public function assertEquals($expected, $actual, string $message = ''): void {
    if ($expected !== $actual) {
      throw new \Exception($message ?: "Expected '{$expected}' but got '{$actual}'");
    }
  }

  public function assertTrue(bool $condition, string $message = ''): void {
    if (!$condition) {
      throw new \Exception($message ?: "Expected true but got false");
    }
  }

  public function assertFalse(bool $condition, string $message = ''): void {
    if ($condition) {
      throw new \Exception($message ?: "Expected false but got true");
    }
  }
}
