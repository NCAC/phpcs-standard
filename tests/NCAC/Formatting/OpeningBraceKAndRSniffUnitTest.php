<?php

declare(strict_types=1);

namespace NCAC\Sniffs\Formatting;

use NCAC\Tests\SniffUnitTest;

/**
 * Unit test class for the OpeningBraceKAndRSniff.
 * @package NCAC\Sniffs\Formatting
 * @author  NCAC
 * @license MIT
 */
class OpeningBraceKAndRSniffUnitTest extends SniffUnitTest {
  /**
   * @dataProvider fixtureProvider
   * @testdox Fixture with $fixture_file
   * Runs each fixture individually using the parent implementation.
   *
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
      case 'OpeningBraceKAndRSniffUnitTest.good.inc':
        return [];
      case 'OpeningBraceKAndRSniffUnitTest.bad.inc':
        return [
          26 => 1,  // Interface TestInterface - brace on wrong line
          34 => 1,  // Trait TestTrait - brace on wrong line
          44 => 1   // Function bad_function - brace on wrong line
        ];
      default:
        return [];
    }
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
   * Returns the path to the ruleset XML file for this test.
   */
  protected function getStandard(): string {
    return __DIR__ . '/ruleset.formatting.openingBraceKAndR.xml';
  }

}
