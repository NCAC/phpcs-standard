<?php

namespace NCAC\Tests\Formatting;

use NCAC\Tests\SniffUnitTest;

/**
 * Unit test class for the ClassOpeningSpacingSniff.
 *
 * @package NCAC\Tests\Formatting
 */
class ClassOpeningSpacingSniffUnitTest extends SniffUnitTest {

  /**
   * Returns the lines where errors should occur for each fixture file.
   *
   * @param string $test_file The name of the file being tested.
   * @return array<int, int>
   */
  public function getErrorList(string $test_file): array {
    switch ($test_file) {
      case 'ClassOpeningSpacingSniffUnitTest.good.inc':
        return [];
      case 'ClassOpeningSpacingSniffUnitTest.bad.inc':
        return [
          3 => 1, // no space
          21 => 1, // TooMuchClassSpacing - too many blank lines after opening brace
          // 22 => 1
        ];
      default:
        return [];
    }
  }

  /**
   * Returns the PHPCS standard (ruleset) to use for this test.
   * Uses a minimal ruleset that only enables ClassOpeningSpacing.
   *
   * @return string
   */
  protected function getStandard(): string {
    return __DIR__ . '/ruleset.formatting.classOpeningSpacing.xml';
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
   * @param string $fixture_file The fixture file to test.
   */
  public function testFixture(string $fixture_file): void {
    parent::testFixture($fixture_file);
  }
}
