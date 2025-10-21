<?php

declare(strict_types=1);

namespace NCAC\Tests\ControlStructures;

use NCAC\Tests\SniffUnitTest;

/**
 * Unit test class for the SwitchDeclarationSniff.
 *
 * Tests switch structure rules (not keyword case):
 * - No space before colon in case/default statements
 * - Non-empty case/default blocks
 * - No blank lines before break statements
 * - Mandatory break statements
 * - Required default case
 * - At least one case statement required
 *
 * @package NCAC\Sniffs\ControlStructures
 * @author  NCAC
 * @license MIT
 */
class SwitchDeclarationSniffUnitTest extends SniffUnitTest {

  /**
   * Returns the lines where errors should occur for each fixture file.
   *
   * @param string $test_file The name of the file being tested.
   * @return array<int, int> Expected errors per line
   */
  public function getErrorList(string $test_file): array {
    switch ($test_file) {
      case 'SwitchDeclarationSniffUnitTest.good.inc':
        return [];

      case 'SwitchDeclarationSniffUnitTest.bad.inc':
        return [
          // Actual errors detected by the sniff
          9   => 1, // Space before colon in case
          41  => 1, // Missing break statement
          48  => 1, // Blank line before break
          63  => 1, // Missing default case
          70  => 1, // Missing case statement (switch with only default)
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
    return __DIR__ . '/ruleset.controlStructures.switchDeclaration.xml';
  }

}
