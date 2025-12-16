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
          3 => 1,   // if ($a): (detection only)
          5 => 1,   // elseif ($b): (detection only)
          7 => 1,   // else: (detection only)
          9 => 1,   // endif; (detection only)
          11 => 2,  // while ($i < 10): (detection only - opening + closing)
          13 => 1,  // endwhile; (detection only)
          15 => 2,  // for ($i = 0; $i < 10; $i++): (detection only - opening + closing)
          17 => 1,  // endfor; (detection only)
          19 => 2,  // foreach ($arr as $v): (detection only - opening + closing)
          21 => 1,  // endforeach; (detection only)
          23 => 2,  // switch ($x): (detection only - opening + closing)
          29 => 1   // endswitch; (detection only)
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
