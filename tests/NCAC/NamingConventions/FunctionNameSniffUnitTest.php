<?php

declare(strict_types=1);

namespace NCAC\Tests\NamingConventions;

use NCAC\Tests\SniffUnitTest;

/**
 * Unit test class for the FunctionNameSniff.
 *
 * Tests snake_case naming convention for global functions:
 * - Validates snake_case requirement for global functions
 * - Ensures class methods are ignored (handled by different sniff)
 * - Tests automatic fixing capability
 * - Verifies StringCaseHelper integration
 *
 * @package NCAC\Sniffs\NamingConventions
 * @author  NCAC
 * @license MIT
 */
class FunctionNameSniffUnitTest extends SniffUnitTest {

  /**
   * Current test file being processed.
   * @var string
   */
  private string $currentTestFile = '';

  /**
   * @dataProvider fixtureProvider
   * @testdox Fixture with $fixture_file
   * Runs each fixture individually using the parent implementation.
   *
   */
  public function testFixture(string $fixture_file): void {
    $this->currentTestFile = $fixture_file;
    parent::testFixture($fixture_file);
  }

  /**
   * Returns the lines where errors should occur for each fixture file.
   *
   * @param string $test_file The name of the file being tested.
   * @return array<int, int> Expected errors per line
   */
  public function getErrorList(string $test_file): array {
    switch ($test_file) {
      case 'FunctionNameSniffUnitTest.good.inc':
        return [];

      case 'FunctionNameSniffUnitTest.bad.inc':
        return [
          // Global functions with incorrect naming
          8   => 1, // camelCase function
          13   => 1, // PascalCase function
          18   => 1, // mixedCase function
          23  => 1, // function with numbers
          28  => 1, // function with abbreviation
          // Class methods should be ignored (no errors)
          // Lines 20-25 contain class methods - should not trigger errors
        ];

      case 'FunctionNameSniffUnitTest.drupal.inc':
        // All functions should be valid with Drupal options enabled
        return [];

      case 'FunctionNameSniffUnitTest.drupal-disabled.inc':
        // Double underscores and leading underscores should be rejected when options are disabled
        return [
          9   => 1, // mymodule_preprocess_node__homepage (has __)
          13  => 1, // theme_preprocess_paragraph__chapitres (has __)
          18  => 1, // _mymodule_internal_helper (has leading _)
          22  => 1, // _another_private_function (has leading _)
        ];

      case 'FunctionNameSniffUnitTest.drupal-fix.inc':
        // Test PHPCBF fixes with Drupal options enabled (preserves __ and _)
        return [
          10  => 1, // calculateTotalPrice (should fix to calculate_total_price)
          15  => 1, // GetUserData (should fix to get_user_data)
          30  => 1, // _calculatePrice (should fix to _calculate_price, preserve _)
          35  => 1, // mymodule_preprocessNode__customPage (should fix to mymodule_preprocess_node__custom_page, preserve __)
        ];

      default:
        return [];
    }
  }

  /**
   * Returns the lines where warnings should occur for each fixture file.
   *
   * @param string $test_file The name of the file being tested.
   * @return array<int, int> Expected warnings per line (none for this sniff)
   */
  public function getWarningList(string $test_file): array {
    return [];
  }

  /**
   * Returns the path to the ruleset XML file for this test.
   * 
   * @return string Path to the ruleset file.
   */
  protected function getStandard(): string {
    // Use Drupal-specific ruleset for Drupal test files
    if (
      strpos($this->currentTestFile, '.drupal.inc') !== false ||
      strpos($this->currentTestFile, '.drupal-fix.inc') !== false
    ) {
      return __DIR__ . '/ruleset.namingConventions.functionName.drupal.xml';
    }

    // Use default ruleset for all other test files
    return __DIR__ . '/ruleset.namingConventions.functionName.xml';
  }

}
