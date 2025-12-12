<?php

namespace NCAC\Tests\NamingConventions;

use NCAC\Tests\SniffUnitTest;

/**
 * Unit test class for the PascalCaseClassNameSniff.
 *
 * @package NCAC\Tests\NamingConventions
 */
class PascalCaseClassNameSniffUnitTest extends SniffUnitTest {

  /**
   * @dataProvider fixtureProvider
   * @testdox Fixture with $fixture_file
   * Runs each fixture individually using the parent implementation.
   */
  public function testFixture(string $fixture_file): void {
    parent::testFixture($fixture_file);
  }

  /**
   * Returns the lines where errors should occur for each fixture file.
   */
  public function getErrorList(string $test_file): array {
    switch ($test_file) {
      case 'PascalCaseClassNameSniffUnitTest.good.inc':
        return [];

      case 'PascalCaseClassNameSniffUnitTest.bad.inc':
        return [
          6 => 1,   // class my_class_name
          14 => 1,  // class camelCaseClassName  
          23 => 1,  // class lowercase
          30 => 1,  // class Mixed_case_Name
          37 => 1,  // interface user_interface
          44 => 1,  // trait helper_trait
          53 => 1,  // class very_long_class_name_here
        ];

      default:
        return [];
    }
  }

  /**
   * Returns the lines where warnings should occur for each fixture file.
   */
  public function getWarningList(string $test_file): array {
    return [];
  }

  /**
   * Returns the path to the ruleset XML file for this test.
   */
  protected function getStandard(): string {
    return __DIR__ . '/ruleset.namingConventions.pascalCaseClassName.xml';
  }
}
