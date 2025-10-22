<?php declare(strict_types=1);

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
   * Returns the lines where errors should occur for each fixture file.
   *
   * @param string $test_file The name of the file being tested.
   * @return array<int, int>
   */
  public function getErrorList(string $test_file): array {
    switch ($test_file) {

      case 'OpeningBraceKAndRSniffUnitTest.good.inc':
        return [];
        break;
      case 'OpeningBraceKAndRSniffUnitTest.bad.inc':
        return [
          5 => 1
        ];
        break;
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
