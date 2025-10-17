<?php

namespace NCAC\Tests\Formatting;

use NCAC\Tests\SniffUnitTest;


/**
 * Unit test class for the ClassClosingSpacingSniff.
 *
 * @package NCAC\Tests\Formatting
 */
class ClassOpeningClosingSpacingSniffUnitTest extends SniffUnitTest {
  /**
   * Returns the lines where errors should occur for each fixture file.
   *
   * @param string $test_file The name of the file being tested.
   * @return array<int, int>
   */
  public function getErrorList(string $test_file): array {
    switch ($test_file) {

      case 'ClassOpeningClosingSpacingSniffUnitTest.good.inc':
        return [];
        break;
      case 'ClassOpeningClosingSpacingSniffUnitTest.bad.inc':
        return [
          5 => 1,
          19 => 1
        ];
        break;
      default:
        // No errors expected.
        break;
    }
    return [];
  }

  /**
   * Returns the lines where warnings should occur for each fixture file.
   *
   * @param string $test_file The name of the file being tested.
   * @return array<int, int>
   */
  public function getWarningList(string $test_file): array {
    return [];
  }

  /**
   * @dataProvider fixtureProvider
   * @testdox Fixture with $fixture_file
   * Runs each fixture individually using the parent implementation.
   *
   * @param string $fixture_file
   */
  public function testFixture(string $fixture_file) {
    parent::testFixture($fixture_file);
  }
}
