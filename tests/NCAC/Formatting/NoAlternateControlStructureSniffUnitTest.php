<?php

namespace NCAC\Tests\Formatting;

use NCAC\Tests\SniffUnitTest;

/**
 * Unit test class for the NoAlternateControlStructureSniff.
 *
 * @package NCAC\Tests\Formatting
 * @author  NCAC
 * @license MIT
 */
class NoAlternateControlStructureSniffUnitTest extends SniffUnitTest {

  /**
   * Returns the lines where errors should occur for each fixture file.
   *
   * @param string $test_file The name of the file being tested.
   * @return array<int, int>
   */
  public function getErrorList(string $test_file): array {
    switch ($test_file) {

      case 'NoAlternateControlStructureSniffUnitTest.good.inc':
        return [];

      case 'NoAlternateControlStructureSniffUnitTest.bad.inc':
        return [
          3 => 1,
          5 => 1,
          7 => 1,
          9 => 1,
          11 => 1,
          13 => 1,
          15 => 1,
          17 => 1,
          19 => 1,
          21 => 1,
          23 => 1,
          29 => 1
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
   * @dataProvider fixtureProvider
   * @testdox Fixture with $fixture_file
   * Runs each fixture individually using the parent implementation.
   *
   */
  public function testFixture(string $fixture_file): void {
    parent::testFixture($fixture_file);
  }

  protected function getStandard(): string {
    return __DIR__ . '/ruleset.formatting.noAlternateControlStructure.xml';
  }

}
