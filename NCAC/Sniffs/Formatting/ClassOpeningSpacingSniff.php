<?php

declare(strict_types=1);

namespace NCAC\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * NCAC Coding Standard - ClassOpeningSpacingSniff
 *
 * Enforces strict formatting for class, trait, and interface opening braces:
 * - Ensures exactly N blank lines after the opening brace (configurable via $linesCount)
 * - Supports automatic fixing through phpcbf
 * - Handles edge cases like minified code and missing whitespace
 * - Applies to classes, traits, and interfaces uniformly
 *
 * Example with $linesCount = 1:
 *   class MyClass {
 *   <exactly 1 blank line>
 *     // class content starts here
 *   }
 *
 * @category CodingStandard
 * @package  NCAC\Sniffs\Formatting
 * @author   NCAC
 * @license  MIT
 * @link     https://github.com/ncac-php/standard
 */
class ClassOpeningSpacingSniff implements Sniff {

  /**
   * Number of blank lines expected after the opening brace.
   *
   * This property can be configured in ruleset.xml to customize the exact
   * number of blank lines required after class/trait/interface opening braces.
   *
   * @var int The number of blank lines (default: 1)
   */
  public int $linesCount = 1;

  /**
   * Registers the tokens this sniff wants to listen for.
   *
   * This sniff processes T_CLASS, T_TRAIT, and T_INTERFACE tokens to enforce
   * consistent spacing after their opening braces according to NCAC standards.
   *
   * @return array<int, int> List of token codes this sniff listens to.
   */
  public function register(): array {
    // Listen for class, trait, and interface declarations
    return [T_CLASS, T_TRAIT, T_INTERFACE];
  }

  /**
   * Processes class, trait, and interface declarations to enforce blank line spacing.
   *
   * This method analyzes the spacing after opening braces of classes, traits, and interfaces.
   * It calculates the current number of blank lines and compares it to the required count,
   * then applies automatic fixes if necessary to ensure compliance with NCAC standards.
   *
   * @param  File $phpcs_file The PHP_CodeSniffer file being analyzed.
   * @param  int  $stack_ptr  The position of the T_CLASS/T_TRAIT/T_INTERFACE token in the stack.
   */
  public function process(File $phpcs_file, $stack_ptr) {
    $tokens = $phpcs_file->getTokens();
    $class_token = $tokens[$stack_ptr];
    $open_class_token = $class_token['scope_opener']; // T_OPEN_CURLY_BRACKET
    $next_token = $open_class_token + 1;

    // Step 1: Locate the first meaningful content after the opening brace.
    // We skip whitespace but NOT comments, as comments are valid content.
    $first_content_token = $phpcs_file->findNext(
      T_WHITESPACE,
      $next_token,
      null,
      true
    );

    // Step 2: Calculate the number of blank lines between opening brace and content.
    // This determines if the current spacing matches our requirements.
    $open_line = $tokens[$open_class_token]['line'];
    $first_content_line = $tokens[$first_content_token]['line'];
    $lines_between = $first_content_line - $open_line - 1;
    if ($lines_between < 0) {
      $lines_between = 0;
    }

    // Step 3: Collect all whitespace tokens between opening brace and first content.
    // This helps us target all tokens that need to be modified during auto-fixing.
    $whitespace_tokens = [];
    for ($i = $open_class_token + 1; $i < $first_content_token; $i++) {
      if ($tokens[$i]['code'] === T_WHITESPACE) {
        $whitespace_tokens[] = $i;
      }
    }

    // Step 4: Use opening brace as error reporting location (always modifiable)
    $error_token = $open_class_token;

    // Step 5: Apply automatic fix if spacing doesn't match requirements.
    // This ensures consistent formatting across all class-like structures.
    if ($lines_between !== $this->linesCount) {
      $fix = $phpcs_file->addFixableError(
        sprintf('There must be exactly %d blank line(s) after the class opening brace.', $this->linesCount),
        $error_token,
        'InvalidClassOpeningSpacing'
      );
      if (!$fix) {
        return;
      }
      $phpcs_file->fixer->beginChangeset();

      // Create the correct whitespace: newlines only (preserve existing indentation)
      $correct_newlines = str_repeat("\n", $this->linesCount + 1);

      if (!empty($whitespace_tokens)) {
        // Find indentation of the first content line to preserve it
        $first_whitespace_content = $tokens[$whitespace_tokens[0]]['content'];
        $lines = explode("\n", $first_whitespace_content);
        $last_line_indentation = end($lines); // Get indentation of the last line

        // Replace the first whitespace token with correct content and preserve indentation
        $replacement = $correct_newlines . $last_line_indentation;
        $phpcs_file->fixer->replaceToken($whitespace_tokens[0], $replacement);

        // Remove all other whitespace tokens
        for ($i = 1; $i < count($whitespace_tokens); $i++) {
          $phpcs_file->fixer->replaceToken($whitespace_tokens[$i], '');
        }
      } else {
        // Insert newlines when no whitespace exists after opening brace
        $phpcs_file->fixer->addContent($open_class_token, $correct_newlines);
      }
      $phpcs_file->fixer->endChangeset();
    }
  }

}
