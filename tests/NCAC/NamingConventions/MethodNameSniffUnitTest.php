<?php

declare(strict_types=1);

namespace NCAC\Tests\NamingConventions;

use NCAC\Tests\SniffUnitTest;

/**
 * Unit test class for the MethodNameSniff.
 *
 * Tests camelCase naming convention for class and trait methods:
 * - Validates camelCase requirement for class and trait methods
 * - Ensures magic methods (starting with __) are excluded from rules
 * - Ensures global functions are ignored (handled by FunctionNameSniff)
 * - Tests various naming violations: snake_case, PascalCase, ALL_CAPS, mixed cases
 * - Verifies StringCaseHelper integration
 *
 * @package NCAC\Sniffs\NamingConventions
 * @author  NCAC
 * @license MIT
 */
class MethodNameSniffUnitTest extends SniffUnitTest {

  /**
   * Returns the lines where errors should occur for each fixture file.
   *
   * @param string $test_file The name of the file being tested.
   * @return array<int, int> Expected errors per line
   */
  public function getErrorList(string $test_file): array {
    
    switch ($test_file) {
      case 'MethodNameSniffUnitTest.good.inc':
        return [];

      case 'MethodNameSniffUnitTest.bad.inc':
        return [
          13 => 1,  // Method name 'get_instance' must be in camelCase.
          20 => 1,  // Method name 'my_method' must be in camelCase.
          24 => 1,  // Method name 'ParseXml' must be in camelCase.
          40 => 1,  // Method name 'handle_event' must be in camelCase.
          51 => 1,  // Method name 'GET_DATA' must be in camelCase.
          69 => 1,  // Method name 'trait_method' must be in camelCase.
          73 => 1,  // Method name 'do_complex_operation' must be in camelCase.
          87 => 1,  // Method name 'invalid_method' must be in camelCase.
          91 => 1  // Method name 'internal_helper' must be in camelCase.
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
    return __DIR__ . '/ruleset.namingConventions.methodName.xml';
  }

}
