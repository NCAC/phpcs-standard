<?php declare(strict_types=1);
/**
 * NCAC Coding Standard - SwitchDeclarationSniff
 *
 * Enforces strict formatting and structure for SWITCH statements:
 * - All 'switch', 'case', and 'default' keywords must be lowercase.
 * - No space is allowed before the colon in 'case' and 'default' statements.
 * - No blank lines are allowed before break statements.
 * - CASE blocks must not be empty.
 * - DEFAULT blocks must not be empty (or must contain a comment if intentionally empty).
 * - Every SWITCH must contain a DEFAULT case.
 * - Every SWITCH must contain at least one CASE statement.
 * - Each CASE and DEFAULT block must end with a break statement (mandatory, no return/throw/exit/goto allowed).
 *
 * @category CodingStandard
 * @package  NCAC\Sniffs\ControlStructures
 * @author   NCAC
 * @license  MIT
 * @link     https://github.com/ncac-php/standard
 */

namespace NCAC\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class SwitchDeclarationSniff implements Sniff {

  /**
   * Registers the tokens this sniff wants to listen for.
   *
   * This sniff only processes T_SWITCH tokens to analyze switch statement
   * structure and formatting according to NCAC coding standards.
   *
   * @return array<int|string> List of token codes this sniff listens to.
   */
  public function register(): array {
    return [T_SWITCH];
  }

  /**
   * Processes a SWITCH statement and enforces formatting and structure rules.
   *
   * This method validates switch statements against NCAC coding standards:
   * - Checks keyword case sensitivity (switch, case, default)
   * - Validates colon spacing in case/default statements
   * - Ensures proper break statement placement and formatting
   * - Verifies case and default blocks are not empty
   * - Confirms presence of required default case and at least one case
   *
   * @param  File $phpcs_file    The PHP_CodeSniffer file being analyzed.
   * @param  int  $stack_pointer The position of the T_SWITCH token in the token stack.
   *
   * @return void This method does not return a value but reports errors via addError().
   */
  public function process(File $phpcs_file, $stack_pointer) {
    $tokens = $phpcs_file->getTokens();

    // Ensure the SWITCH statement has a valid scope (opening and closing braces).
    // If scope is not properly defined, skip processing to avoid errors.
    if (!isset($tokens[$stack_pointer]['scope_opener']) || !isset($tokens[$stack_pointer]['scope_closer'])) {
      return;
    }

    // Initialize tracking variables for switch statement analysis
    $switch = $tokens[$stack_pointer];           // Current switch token data
    $next_case = $stack_pointer;                 // Pointer for iterating through cases
    $case_count = 0;                            // Count of case statements found
    $found_default = false;                     // Flag to track if default case exists

    // Iterate over all CASE and DEFAULT statements within the SWITCH scope.
    // We also look for nested SWITCH statements to skip them properly.
    while (($next_case = $phpcs_file->findNext([T_CASE, T_DEFAULT, T_SWITCH], ($next_case + 1), $switch['scope_closer'])) !== false) {
      // Skip nested SWITCH statements by jumping to their closing brace.
      // This prevents false positives when analyzing inner switch statements.
      if ($tokens[$next_case]['code'] === T_SWITCH) {
        $next_case = $tokens[$next_case]['scope_closer'];
        continue;
      }

      // Determine if current token is a CASE or DEFAULT statement
      // and update our tracking variables accordingly
      $type = ($tokens[$next_case]['code'] === T_DEFAULT) ? 'Default' : 'Case';
      if ($type === 'Default') {
        $found_default = true;
      } else {
        $case_count++;
      }

      // Rule 1: Enforce lowercase keywords.
      if ($tokens[$next_case]['content'] !== strtolower($tokens[$next_case]['content'])) {
        $expected = strtolower($tokens[$next_case]['content']);
        $error = sprintf("%s keyword must be lowercase; expected '%s' but found '%s'", $type, $expected, $tokens[$next_case]['content']);
        $phpcs_file->addError($error, $next_case, $type.'NotLower');
      }

      // Rule 2: No space before colon in case/default statements.
      // Check if there's whitespace immediately before the colon token.
      if (isset($tokens[$next_case]['scope_opener'])) {
        $opener = $tokens[$next_case]['scope_opener'];
        if ($tokens[$opener - 1]['type'] === 'T_WHITESPACE') {
          $error = sprintf("No space allowed before colon in %s statement", $type);
          $phpcs_file->addError($error, $next_case, 'SpaceBeforeColon'.$type);
        }
      }

      // Rule 3: CASE/DEFAULT blocks must not be empty.
      // Scan the content between scope opener and closer to verify non-empty blocks.
      if (isset($tokens[$next_case]['scope_opener']) && isset($tokens[$next_case]['scope_closer'])) {
        $opener = $tokens[$next_case]['scope_opener'];
        $closer = $tokens[$next_case]['scope_closer'];
        $found_content = false;
        
        // Loop through all tokens in the case/default block
        for ($i = $opener + 1; $i < $closer; $i++) {
          // Skip nested case statements to avoid false positives
          if ($tokens[$i]['code'] === T_CASE) {
            $i = $tokens[$i]['scope_opener'];
            continue;
          }
          // Accept any meaningful content: non-empty tokens, comments, or control flow statements
          if (
            isset(Tokens::$empty_tokens[$tokens[$i]['code']]) === false
            || $tokens[$i]['code'] === T_COMMENT
            || $tokens[$i]['code'] === T_DOC_COMMENT
            || in_array($tokens[$i]['code'], [T_BREAK, T_EXIT, T_RETURN, T_THROW, T_GOTO], true)
          ) {
            $found_content = true;
            break;
          }
        }
        
        // Report errors for empty blocks with appropriate messages
        if ($found_content === false) {
          if ($type === 'Default') {
            $phpcs_file->addError('Comment required for empty DEFAULT case', $next_case, 'EmptyDefault');
          } else {
            $phpcs_file->addError('Empty CASE statements are not allowed', $next_case, 'EmptyCase');
          }
        }
      }

      // Rule 4: No blank lines allowed before break statements.
      // Check spacing between the last content and the break statement.
      if (isset($tokens[$next_case]['scope_closer'])) {
        $closer = $tokens[$next_case]['scope_closer'];
        if ($tokens[$closer]['code'] === T_BREAK) {
          $prev = $phpcs_file->findPrevious(T_WHITESPACE, ($closer - 1), $stack_pointer, true);
          if ($tokens[$prev]['line'] !== ($tokens[$closer]['line'] - 1)) {
            $phpcs_file->addError('Blank lines are not allowed before break statements', $closer, 'SpacingBeforeBreak');
          }
        }
      }

      // Rule 5: Mandatory break statement at the end of each CASE and DEFAULT block.
      // Every case and default must terminate with a break (no fall-through allowed).
      if (isset($tokens[$next_case]['scope_closer'])) {
        $closer = $tokens[$next_case]['scope_closer'];
        if ($tokens[$closer]['code'] !== T_BREAK) {
          $phpcs_file->addError('Each CASE and DEFAULT must end with a break statement', $closer, 'MissingBreak');
        }
      }
    }

    // Rule 6: Every SWITCH statement must contain a DEFAULT case.
    // This ensures comprehensive coverage of all possible values.
    if ($found_default === false) {
      $phpcs_file->addError('All SWITCH statements must contain a DEFAULT case', $stack_pointer, 'MissingDefault');
    }

    // Rule 7: Every SWITCH statement must contain at least one CASE statement.
    // A switch with only a default case is considered invalid structure.
    if ($case_count === 0) {
      $phpcs_file->addError('SWITCH statements must contain at least one CASE statement', $stack_pointer, 'MissingCase');
    }
  }

}
