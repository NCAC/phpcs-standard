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
   */
  protected function getStandard(): string {
    return __DIR__ . '/ruleset.namingConventions.functionName.xml';
  }

}
