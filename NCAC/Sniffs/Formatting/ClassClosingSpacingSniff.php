<?php declare(strict_types=1);

namespace NCAC\Sniffs\Formatting;

use InvalidArgumentException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * NCAC Coding Standard - ClassClosingSpacingSniff
 *
 * Enforces a fixed number of blank lines before the closing brace of classes, traits, and interfaces.
 * This sniff ensures consistent spacing by requiring exactly `$linesCount` blank lines before
 * the closing curly brace of any class-like structure.
 *
 * Features:
 * - Supports automatic fixing (phpcbf)
 * - Configurable number of blank lines via $linesCount property
 * - Robust handling of minified/compact code
 * - Works with classes, traits, and interfaces uniformly
 *
 * Example with $linesCount = 1:
 *   class MyClass {
 *     // class content
 *
 *   }
 *
 * Configuration in ruleset.xml:
 *   <rule ref="NCAC.Formatting.ClassClosingSpacing">
 *     <properties>
 *       <property name="linesCount" value="2"/>
 *     </properties>
 *   </rule>
 *
 * @category CodingStandard
 * @package  NCAC\Sniffs\Formatting
 * @author   NCAC
 * @license  MIT
 * @link     https://github.com/ncac-php/standard
 */
class ClassClosingSpacingSniff implements Sniff {

  /**
   * Number of blank lines required before the class closing brace.
   *
   * This property can be configured in the ruleset.xml file to customize
   * the exact number of blank lines required before closing braces.
   *
   * @var int The number of blank lines (default: 1)
   */
  public int $linesCount = 1;

  /**
   * Registers the tokens this sniff wants to listen for.
   *
   * This sniff processes T_CLASS, T_TRAIT, and T_INTERFACE tokens to enforce
   * consistent spacing before their closing braces according to NCAC standards.
   *
   * @return array<int, int> List of token codes this sniff listens to.
   */
  public function register(): array {
    // Listen for class, trait, and interface declarations
    return [\T_CLASS, \T_TRAIT, \T_INTERFACE];
  }

  /**
   * Processes class, trait, and interface declarations to enforce blank line spacing.
   *
   * This method analyzes the spacing before closing braces of classes, traits, and interfaces.
   * It calculates the current number of blank lines and compares it to the required count,
   * then applies automatic fixes if necessary to ensure compliance with NCAC standards.
   *
   * @param  File $phpcs_file The PHP_CodeSniffer file being analyzed.
   * @param  int  $stack_ptr  The position of the T_CLASS/T_TRAIT/T_INTERFACE token in the stack.
   *
   * @throws InvalidArgumentException When token structure is invalid or malformed.
   */
  public function process(File $phpcs_file, int $stack_ptr) {
    $tokens = $phpcs_file->getTokens();
    $class_token = $tokens[$stack_ptr];

    // Step 1: Extract scope boundaries for the class/trait/interface structure.
    // These tokens define where the body content starts and ends.
    $open_class_token = $class_token['scope_opener']; // T_OPEN_CURLY_BRACKET
    $close_class_token = $class_token['scope_closer']; // T_CLOSE_CURLY_BRACKET

    // Step 2: Locate the last meaningful content before the closing brace.
    // We ignore whitespace and comments to find the actual last code element.
    $last_content_token_before_close = $phpcs_file->findPrevious(
      [\T_WHITESPACE, \T_COMMENT, \T_DOC_COMMENT],
      $close_class_token - 1,
      $open_class_token + 1,
      true
    );

    // Step 3: Calculate the number of blank lines between content and closing brace.
    // For empty classes, we use the opening brace line as reference point.
    $last_line = ($last_content_token_before_close === false)
      ? $tokens[$open_class_token]['line']
      : $tokens[$last_content_token_before_close]['line'];
    $close_line = $tokens[$close_class_token]['line'];
    $lines_between = $close_line - $last_line - 1;
    if ($lines_between < 0) {
      $lines_between = 0;
    }

    // Step 4: Apply automatic fix if spacing doesn't match requirements.
    // This ensures consistent formatting across all class-like structures.
    if ($lines_between !== $this->linesCount) {
      $fix = $phpcs_file->addFixableError(
        \sprintf('There must be exactly %d blank line(s) before the class closing brace.', $this->linesCount),
        $close_class_token,
        'InvalidClassClosingSpacing'
      );
      if ($fix) {
        $phpcs_file->fixer->beginChangeset();
        // Remove all existing whitespace tokens between last content and closing brace
        $next_token = ($last_content_token_before_close === false) ? $open_class_token + 1 : $last_content_token_before_close + 1;
        for ($i = $next_token; $i < $close_class_token; $i++) {
          if ($tokens[$i]['code'] === \T_WHITESPACE) {
            $phpcs_file->fixer->replaceToken($i, '');
          }
        }
        // Insert the exact number of required newlines plus one for the closing brace line
        $newlines = str_repeat("\n", $this->linesCount) . "\n";
        $phpcs_file->fixer->addContentBefore($close_class_token, $newlines);
        $phpcs_file->fixer->endChangeset();
      }
    }
  }

}
