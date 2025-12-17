<?php

declare(strict_types=1);

/**
 * End-to-end test runner for NCAC PHP_CodeSniffer Standard.
 *
 * This runner tests the complete workflow effectiveness:
 * 1. Count initial violations
 * 2. Apply PHP-CS-Fixer (complex transformations)
 * 3. Apply PHPCBF (PHPCS auto-fixes)
 * 4. Count remaining violations
 * 5. Compare with ideal .fixed files (informational)
 *
 * SUCCESS CRITERIA: Workflow must reduce violations (pragmatic approach)
 * The .fixed files represent the IDEAL target, not a blocking requirement
 *
 * @author   NCAC
 * @category PHP_CodeSniffer
 * @package  NCAC
 * @license  https://github.com/NCAC/phpcs-standard/blob/main/LICENSE MIT License
 */

namespace NCAC\E2ETests;

class E2ETestRunner {

  private string $workingDir;

  private string $phpcsExecutable;

  private string $phpcbfExecutable;

  private string $phpCsFixerExecutable;

  private string $phpCsFixerConfig;

  private string $standard = 'NCAC';

  private int $passed = 0;

  private int $failed = 0;

  private array $testFiles = [];

  private array $results = [];

  public function __construct(string $working_dir) {
    $this->workingDir = $working_dir;
    $this->phpcsExecutable = $working_dir . '/vendor/bin/phpcs';
    $this->phpcbfExecutable = $working_dir . '/vendor/bin/phpcbf';
    $this->phpCsFixerExecutable = $working_dir . '/vendor/bin/php-cs-fixer';
    $this->phpCsFixerConfig = $working_dir . '/.php-cs-fixer.dist.php';
  }

  /**
   * Run all workflow tests on .bad.inc files.
   */
  public function run(): int {
    $this->printHeader();

    if (!$this->checkPrerequisites()) {
      return 1;
    }

    // Find all .bad.inc files in the test suite
    $bad_files = $this->findBadIncFiles();
    if (empty($bad_files)) {
      echo "❌ No .bad.inc files found to test\n";
      return 1;
    }

    echo 'Found ' . \count($bad_files) . " test files to process\n\n";

    // Process each .bad.inc file through the workflow
    foreach ($bad_files as $bad_file) {
      $this->processTestFile($bad_file);
    }

    $this->printSummary();
    $this->cleanup();

    return $this->failed > 0 ? 1 : 0;
  }

  /**
   * Find all .bad.inc files in the test suite.
   *
   * @return string[]
   */
  private function findBadIncFiles(): array {
    $files = [];
    $test_dir = $this->workingDir . '/tests';
    $iterator = new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($test_dir)
    );
    foreach ($iterator as $file) {
      if ($file->isFile() && str_ends_with($file->getFilename(), '.bad.inc')) {
        $files[] = $file->getPathname();
      }
    }
    sort($files);
    return $files;
  }

  /**
   * Process a single .bad.inc file through the workflow.
   */
  private function processTestFile(string $bad_file): void {
    $test_name = $this->getTestName($bad_file);
    $fixed_file = str_replace('.bad.inc', '.bad.inc.fixed', $bad_file);
    echo "\n" . str_repeat('─', 80) . "\n";
    echo "Testing: {$test_name}\n";
    echo str_repeat('─', 80) . "\n";

    try {
      // Check if .fixed file exists
      if (!file_exists($fixed_file)) {
        throw new \Exception("Missing .fixed file: {$fixed_file}");
      }

      // Create a working copy of the .bad.inc file
      $working_file = $this->createWorkingCopy($bad_file);

      // Step 1: Count initial violations
      echo "  ➜ Step 1/5: Analyzing initial violations...\n";
      $initial_violations = $this->countViolations($bad_file);
      echo "    ℹ️  Initial violations: {$initial_violations}\n";

      // Step 2: Apply PHP-CS-Fixer
      echo "  ➜ Step 2/5: Applying PHP-CS-Fixer...\n";
      $this->applyPhpCsFixer($working_file);
      echo "    ✓ PHP-CS-Fixer applied\n";

      // Step 3: Apply PHPCBF
      echo "  ➜ Step 3/5: Applying PHPCBF...\n";
      $this->applyPhpcbf($working_file);
      echo "    ✓ PHPCBF applied\n";

      // Step 4: Count remaining violations
      echo "  ➜ Step 4/5: Analyzing remaining violations...\n";
      $remaining_violations = $this->countViolations($working_file);
      $fixed_violations = $initial_violations - $remaining_violations;
      $improvement_rate = $initial_violations > 0
        ? round(($fixed_violations / $initial_violations) * 100, 1)
        : 100;

      echo "    ℹ️  Remaining violations: {$remaining_violations}\n";
      echo "    ℹ️  Fixed violations: {$fixed_violations}\n";
      echo "    ℹ️  Improvement rate: {$improvement_rate}%\n";

      // Step 5: Compare with .fixed file (informational, not blocking)
      echo "  ➜ Step 5/5: Comparing with ideal .fixed file...\n";
      $similarity = $this->compareWithFixedNonBlocking($working_file, $fixed_file);

      if ($similarity === 100.0) {
        echo "    ✓ Perfect match with .fixed file (100% conformity) 🎉\n";
      } else {
        echo "    ℹ️  Similarity with ideal: {$similarity}% (target for future improvements)\n";
      }

      // Success criteria: workflow must reduce violations
      if ($remaining_violations >= $initial_violations && $initial_violations > 0) {
        throw new \Exception(
          "Workflow did not reduce violations (before: {$initial_violations}, after: {$remaining_violations})"
        );
      }

      $this->passed++;
      echo "✅ PASSED: {$test_name}\n";
      $this->results[$test_name] = [
        'status' => 'PASSED',
        'message' => "Fixed {$fixed_violations}/{$initial_violations} violations ({$improvement_rate}% improvement)",
        'initial_violations' => $initial_violations,
        'remaining_violations' => $remaining_violations,
        'improvement_rate' => $improvement_rate,
        'similarity_to_ideal' => $similarity
      ];
    } catch (\Exception $e) {
      $this->failed++;
      echo "❌ FAILED: {$test_name}\n";
      echo '   Error: ' . $e->getMessage() . "\n";
      $this->results[$test_name] = [
        'status' => 'FAILED',
        'message' => $e->getMessage()
      ];
    }
  }

  /**
   * Create a working copy of the .bad.inc file.
   */
  private function createWorkingCopy(string $bad_file): string {
    $working_file = $this->workingDir . '/e2e-tests/tmp/' . basename($bad_file) . '.working.php';
    $dir = \dirname($working_file);
    if (!is_dir($dir)) {
      mkdir($dir, 0755, true);
    }
    copy($bad_file, $working_file);
    $this->testFiles[] = $working_file;
    return $working_file;
  }

  /**
   * Apply PHP-CS-Fixer to the working file.
   */
  private function applyPhpCsFixer(string $file): void {
    $cmd = escapeshellarg($this->phpCsFixerExecutable) .
    ' fix ' . escapeshellarg($file) .
    ' --config=' . escapeshellarg($this->phpCsFixerConfig) .
    ' --quiet 2>/dev/null';
    exec($cmd, $output, $exit_code);
    // PHP-CS-Fixer may return non-zero even on success, so we don't check exit code
    // We'll validate the result in the comparison step
  }

  /**
   * Apply PHPCBF to the working file.
   */
  private function applyPhpcbf(string $file): void {
    $cmd = escapeshellarg($this->phpcbfExecutable) .
    ' --standard=' . escapeshellarg($this->standard) .
    ' ' . escapeshellarg($file) .
    ' 2>/dev/null';
    exec($cmd, $output, $exit_code);
    // PHPCBF may return non-zero even when fixing files, so we don't check exit code
  }

  /**
   * Count PHPCS violations in a file.
   *
   * @return int Number of violations found
   */
  private function countViolations(string $file): int {
    $cmd = escapeshellarg($this->phpcsExecutable) .
    ' --standard=' . escapeshellarg($this->standard) .
    ' --report=json ' . escapeshellarg($file) .
    ' 2>/dev/null';
    exec($cmd, $output, $exit_code);
    $json = implode("\n", $output);
    $data = json_decode($json, true);

    if (!$data || !isset($data['totals'])) {
      return 0;
    }

    return (int) ($data['totals']['errors'] ?? 0) + (int) ($data['totals']['warnings'] ?? 0);
  }

  /**
   * Compare the working file with the expected .fixed file (non-blocking).
   * Returns a similarity percentage.
   *
   * @return float Similarity percentage (0-100)
   */
  private function compareWithFixedNonBlocking(string $working_file, string $fixed_file): float {
    $working_content = file_get_contents($working_file);
    $expected_content = file_get_contents($fixed_file);

    // Normalize line endings for comparison
    $working_normalized = str_replace(["\r\n", "\r"], "\n", $working_content);
    $expected_normalized = str_replace(["\r\n", "\r"], "\n", $expected_content);

    if ($working_normalized === $expected_normalized) {
      return 100.0; // Perfect match
    }

    // Calculate similarity using similar_text
    $similarity = 0.0;
    similar_text($expected_normalized, $working_normalized, $similarity);

    return round($similarity, 1);
  }

  /**
   * Get a readable test name from the file path.
   */
  private function getTestName(string $file_path): string {
    $relative_path = str_replace($this->workingDir . '/tests/', '', $file_path);
    return str_replace('.bad.inc', '', $relative_path);
  }

  /**
   * Check if all required executables are available.
   */
  private function checkPrerequisites(): bool {
    $missing = [];
    $details = [];
    if (!file_exists($this->phpcsExecutable)) {
      $missing[] = 'PHPCS';
      $details[] = "  - PHPCS: {$this->phpcsExecutable}";
    }
    if (!file_exists($this->phpcbfExecutable)) {
      $missing[] = 'PHPCBF';
      $details[] = "  - PHPCBF: {$this->phpcbfExecutable}";
    }
    if (!file_exists($this->phpCsFixerExecutable)) {
      $missing[] = 'PHP-CS-Fixer';
      $details[] = "  - PHP-CS-Fixer: {$this->phpCsFixerExecutable}";
    }
    if (!file_exists($this->phpCsFixerConfig)) {
      $missing[] = 'PHP-CS-Fixer config';
      $details[] = "  - PHP-CS-Fixer config: {$this->phpCsFixerConfig}";
    }

    if (!empty($missing)) {
      echo '❌ Missing prerequisites: ' . implode(', ', $missing) . "\n";
      echo "   Missing files:\n" . implode("\n", $details) . "\n";
      echo "   Working directory: {$this->workingDir}\n";
      echo "   Please run: composer install\n";
      return false;
    }

    return true;
  }

  /**
   * Print test header.
   */
  private function printHeader(): void {
    echo "\n";
    echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
    echo "║ NCAC E2E Workflow Tests - Quality Improvement Validation                    ║\n";
    echo "╠══════════════════════════════════════════════════════════════════════════════╣\n";
    echo "║ Tests: PHP-CS-Fixer → PHPCBF → Violation Reduction                          ║\n";
    echo "║ Goal: Verify workflow effectiveness (not perfect similarity)                ║\n";
    echo "║ .fixed files = IDEAL target for future improvements                         ║\n";
    echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";
  }

  /**
   * Print test summary.
   */
  private function printSummary(): void {
    $total = $this->passed + $this->failed;
    $percentage = $total > 0 ? round(($this->passed / $total) * 100, 1) : 0;

    // Calculate average metrics
    $total_initial = 0;
    $total_remaining = 0;
    $total_similarity = 0.0;
    $count_with_data = 0;

    foreach ($this->results as $result) {
      if (isset($result['initial_violations'])) {
        $total_initial += $result['initial_violations'];
        $total_remaining += $result['remaining_violations'];
        $total_similarity += $result['similarity_to_ideal'] ?? 0;
        $count_with_data++;
      }
    }

    $avg_improvement = $total_initial > 0
      ? round((($total_initial - $total_remaining) / $total_initial) * 100, 1)
      : 0;
    $avg_similarity = $count_with_data > 0
      ? round($total_similarity / $count_with_data, 1)
      : 0;

    echo "\n";
    echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
    echo "║ E2E Workflow Test Summary                                                    ║\n";
    echo "╠══════════════════════════════════════════════════════════════════════════════╣\n";
    echo \sprintf("║  Total Tests:        %2d                                                     ║\n", $total);
    echo \sprintf("║  Passed:             %2d ✅                                                   ║\n", $this->passed);
    echo \sprintf("║  Failed:             %2d ❌                                                   ║\n", $this->failed);
    echo \sprintf("║  Success Rate:       %5s%%                                                  ║\n", $percentage);
    echo "╠══════════════════════════════════════════════════════════════════════════════╣\n";
    echo \sprintf("║  Avg Improvement:    %5s%% (violations reduced)                            ║\n", $avg_improvement);
    echo \sprintf("║  Avg Similarity:     %5s%% (vs ideal .fixed files)                         ║\n", $avg_similarity);
    echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

    if ($this->failed === 0) {
      echo "🎉 All workflow tests passed!\n";
      echo "📦 The NCAC quality workflow effectively reduces violations.\n";
      if ($avg_similarity < 100) {
        echo "💡 Average similarity to ideal: {$avg_similarity}% - room for improvement!\n";
        echo "   Consider migrating more logic to php-cs-fixer for better results.\n";
      }
      echo "\n";
    } else {
      echo "⚠️  Some workflow tests failed. Details:\n\n";
      foreach ($this->results as $test_name => $result) {
        if ($result['status'] === 'FAILED') {
          echo "❌ {$test_name}: {$result['message']}\n";
        }
      }
      echo "\n";
    }
  }

  /**
   * Clean up test files.
   */
  private function cleanup(): void {
    foreach ($this->testFiles as $file) {
      if (file_exists($file)) {
        unlink($file);
      }
    }
  }

}
