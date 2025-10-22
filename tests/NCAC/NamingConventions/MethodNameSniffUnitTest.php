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
    $error_list = [];
    
    switch ($test_file) {
      case 'MethodNameSniffUnitTest.good.inc':
        $error_list = [];
        break;
      case 'MethodNameSniffUnitTest.bad.inc':
        $error_list = [
          
          // Invalid snake_case method names
          10 => 1,  // public function my_method() - should be camelCase
          14 => 1,  // public function calculate_total() - should be camelCase
          18 => 1,  // private function parse_data() - should be camelCase
          22 => 1,  // protected function handle_special_case() - should be camelCase

          // Static methods - incorrectly in snake_case
          27 => 1,  // public static function get_instance() - should be camelCase

          // PascalCase methods - also invalid (should be camelCase)
          32 => 1,  // public function ParseXml() - should be camelCase
          36 => 1,  // protected function HandleEvent() - should be camelCase

          // ALL_CAPS method - invalid
          41 => 1,  // private function GET_DATA() - should be camelCase

          // Mixed case method - invalid
          46 => 1,  // public function convertXML_file() - should be camelCase

          // Method calls with snake_case - invalid
          60 => 1,  // public function call_other_methods() - should be camelCase

          // Trait with improperly named methods
          73 => 1,  // public function trait_method() - should be camelCase
          77 => 1,  // private function helper_function() - should be camelCase
          82 => 1,  // protected function DoComplexOperation() - should be camelCase

          // anonymous class
          97 => 1,   // Method name 'invalid_method' must be in camelCase.
          101 => 2  // Method name 'internal_helper' must be in camelCase.
        ];
        break;

      default:
        $error_list = [];
        break;
    }
          
    return $error_list;
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
