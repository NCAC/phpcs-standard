<?php

declare(strict_types=1);

namespace NCAC\E2ETests;

/**
 * E2E test to verify that all .fixed files are correct.
 *
 * This test takes all .bad.inc files from the project, applies PHPCBF
 * with the appropriate standard, and verifies that the result matches
 * exactly the expected .fixed file.
 *
 * This allows the maintainer to manually define the expected result
 * and ensure that PHPCBF produces exactly what is intended.
 */
class FixedFilesValidationTest extends E2ETest {

  public function getName(): string {
    return "Fixed Files Validation Test";
  }

  public function run(): void {
    $this->testAllFixedFiles();
  }

  /**
   * Tests all .fixed files in the project.
   */
  private function testAllFixedFiles(): void {
    $this->step("Searching for all .bad.inc files...");

    $fixtures = $this->discoverFixtures();

    if (empty($fixtures)) {
      $this->step("No .bad.inc files found");
      return;
    }

    $this->success("Found " . count($fixtures) . " files to test");

    foreach ($fixtures as $fixture) {
      $this->testSingleFixture($fixture);
    }
  }

  /**
   * Discovers all .bad.inc files with their corresponding .fixed files.
   *
   * @return array<array{bad: string, fixed: string, ruleset: string, name: string}>
   */
  private function discoverFixtures(): array {
    $fixtures = [];
    $tests_dir = $this->runner->getWorkingDir() . '/tests';

    // Parcourir récursivement le dossier tests/
    $iterator = new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($tests_dir, \RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
      if (!$file->isFile()) {
        continue;
      }

      $filename = $file->getFilename();

      // Chercher les fichiers .bad.inc
      if (preg_match('/^(.+?)\.bad\.inc$/', $filename, $matches)) {
        $base_name = $matches[1];
        $bad_file = $file->getPathname();
        $fixed_file = str_replace('.bad.inc', '.bad.inc.fixed', $bad_file);

        // Check that the .fixed file exists
        if (file_exists($fixed_file)) {
          $ruleset = $this->findRulesetForFixture($bad_file);

          $fixtures[] = [
            'bad' => $bad_file,
            'fixed' => $fixed_file,
            'ruleset' => $ruleset,
            'name' => $base_name,
          ];
        }
      }
    }

    return $fixtures;
  }

  /**
   * Finds the appropriate ruleset for a fixture file.
   * 
   * By default, uses the complete NCAC standard because .fixed files
   * should reflect the result of the complete standard, not an isolated sniff.
   * 
   * Exceptions: certain sniffs may have special rulesets for specific cases
   * (e.g. Drupal, specific tests).
   */
  private function findRulesetForFixture(string $bad_file): string {
    $filename = basename($bad_file);

    // Special cases that require specific rulesets
    $special_cases = [
      'DeclarationSpacingSniffUnitTest' => 'declarationSpacing',
      'SwitchDeclarationSniffUnitTest' => 'switchDeclaration',
      // Add other special cases if needed
    ];

    if (preg_match('/^(.+?)SniffUnitTest\.bad\.inc$/', $filename, $matches)) {
      $sniff_name = $matches[1];

      if (isset($special_cases[$sniff_name])) {
        $dir = dirname($bad_file);
        $ruleset_pattern = $special_cases[$sniff_name];

        // Search for a ruleset containing the specific pattern
        $iterator = new \DirectoryIterator($dir);
        foreach ($iterator as $file) {
          if ($file->isFile() && substr($file->getFilename(), -4) === '.xml') {
            if (strpos(strtolower($file->getFilename()), $ruleset_pattern) !== false) {
              return $file->getPathname();
            }
          }
        }
      }
    }

    // Default: use the special NCAC ruleset for .fixed tests
    // that excludes Slevomat rules that automatically modify code
    return dirname(__DIR__) . '/ruleset-ncac-for-fixed-tests.xml';
  }

  /**
   * Tests an individual fixture.
   */
  private function testSingleFixture(array $fixture): void {
    $name = $fixture['name'];
    $bad_file = $fixture['bad'];
    $fixed_file = $fixture['fixed'];
    $ruleset = $fixture['ruleset'];

    $this->step("Test: $name");

    // Create a temporary copy of the bad file
    $temp_file = $this->runner->createTestFile(
      'fixture-' . basename($name) . '-' . uniqid() . '.php',
      file_get_contents($bad_file)
    );

    // Execute PHPCBF
    $phpcbf_options = [];
    if ($ruleset !== 'NCAC') {
      $phpcbf_options['standard'] = $ruleset;
    }

    // Debug information
    // echo "    Ruleset: $ruleset\n";

    $result = $this->runner->runPhpcbf($temp_file, $phpcbf_options);

    // Read expected and actual content
    $expected_content = file_get_contents($fixed_file);
    $actual_content = file_get_contents($temp_file);

    // Compare
    if ($expected_content === $actual_content) {
      $this->success("✓ $name - .fixed file correct");
    } else {
      // Display differences for debugging
      $this->step("❌ $name - Differences found:");

      $expected_lines = explode("\n", $expected_content);
      $actual_lines = explode("\n", $actual_content);

      $max_lines = max(count($expected_lines), count($actual_lines));
      $diff_count = 0;

      for ($i = 0; $i < $max_lines && $diff_count < 5; $i++) {
        $expected_line = $expected_lines[$i] ?? '<missing>';
        $actual_line = $actual_lines[$i] ?? '<missing>';

        if ($expected_line !== $actual_line) {
          $line_num = $i + 1;
          echo "    Line $line_num:\n";
          echo "      Expected: " . var_export($expected_line, true) . "\n";
          echo "      Actual:   " . var_export($actual_line, true) . "\n";
          $diff_count++;
        }
      }

      if ($diff_count >= 5) {
        echo "    ... (more differences)\n";
      }

      throw new \Exception("The .fixed file for '$name' does not match the PHPCBF result");
    }
  }

}
