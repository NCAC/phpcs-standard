<?php

namespace NCAC\Tests\WhiteSpace;

use NCAC\Tests\SniffUnitTest;

/**
 * Unit test class for the DeclarationSpacingSniff.
 *
 * @package NCAC\Tests\WhiteSpace
 */
class DeclarationSpacingSniffUnitTest extends SniffUnitTest {

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
   *
   * @param string $test_file The name of the file being tested.
   * @return array<int, int>
   */
  public function getErrorList(string $test_file): array {
    switch ($test_file) {

      case 'DeclarationSpacingSniffUnitTest.good.inc':
        return [];

      case 'DeclarationSpacingSniffUnitTest.bad.inc':
        return [
          4 => 1,   // function with 3 spaces
          8 => 1,   // function with 2 spaces
          12 => 1,  // class with 4 spaces
          14 => 1,  // method with 4 spaces
          20 => 1,  // interface with 3 spaces
          26 => 1,  // trait with 2 spaces
          34 => 1,  // enum with 4 spaces
          40 => 1,  // abstract class with 2 spaces
          42 => 1,  // abstract method with 3 spaces
          50 => 1,  // final class with 5 spaces
          52 => 1,  // final method with 2 spaces
        ];

      default:
        return [];
    }
  }

  /**
   * Returns the lines where warnings should occur.
   *
   * @param string $test_file The name of the file being tested.
   * @return array<int, int>
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
    return __DIR__ . '/ruleset.whiteSpace.declarationSpacing.xml';
  }

}
