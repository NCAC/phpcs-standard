<?php declare(strict_types=1);

namespace NCAC\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;


/**
 * NCAC Coding Standard - OpeningBraceKAndRSniff
 *
 * Enforces K&R (Kernighan & Ritchie) style for opening braces:
 * - Opening braces must be on the same line as the declaration
 * - Applies to classes, interfaces, traits, and functions
 * - Provides automatic fixing to move misplaced braces
 * - Handles whitespace normalization between declaration and brace
 * - Ensures consistent brace placement across all code structures
 *
 * Examples of transformations:
 *   class MyClass        →  class MyClass {
 *   {                        
 *   
 *   function foo()       →  function foo() {
 *   {
 *
 * @category CodingStandard
 * @package  NCAC\Sniffs\Formatting
 * @author   NCAC
 * @license  MIT
 * @link     https://github.com/ncac-php/standard
 */
class OpeningBraceKAndRSniff implements Sniff {

  /**
   * Registers the tokens this sniff wants to listen for.
   *
   * This sniff processes declarations of classes, interfaces, traits, and functions
   * to ensure their opening braces follow K&R style positioning rules.
   *
   * @return array<int, int> List of token codes this sniff listens to.
   */
  public function register(): array {
    // Listen for class, interface, trait, and function declarations
    return [T_CLASS, T_INTERFACE, T_TRAIT, T_FUNCTION];
  }

  /**
   * Processes declarations to enforce K&R style opening brace placement.
   *
   * This method analyzes the positioning of opening braces relative to their
   * corresponding declarations. It ensures braces are on the same line as the
   * declaration and applies automatic fixes when violations are detected.
   * The method handles whitespace normalization and provides robust error recovery.
   *
   * @param  File $phpcs_file    The PHP_CodeSniffer file being analyzed.
   * @param  int  $stack_pointer The position of the declaration token in the stack.
   */
  public function process(File $phpcs_file, $stack_pointer) {
    $tokens = $phpcs_file->getTokens();
    // Step 1: Validate that the declaration has a proper opening brace.
    // Interface methods and abstract methods don't have braces, which is normal.
    // We silently skip these cases instead of issuing a warning.
    if (!isset($tokens[$stack_pointer]['scope_opener'])) {
      return;
    }
    $curly_brace = $tokens[$stack_pointer]['scope_opener'];
    // Step 2: Find the last meaningful content before the opening brace.
    // This helps us determine where the declaration actually ends.
    $last_content = $phpcs_file->findPrevious(T_WHITESPACE, ($curly_brace - 1), $stack_pointer, true);
    if ($last_content === false) {
      // Defensive programming: no content found before brace
      return;
    }
    // Step 3: Compare line positions to detect K&R violations.
    // K&R style requires the brace to be on the same line as the declaration.
    $declaration_line = $tokens[$last_content]['line'];
    $brace_line = $tokens[$curly_brace]['line'];

    // Step 4: Apply automatic fix if brace placement violates K&R style.
    if ($brace_line !== $declaration_line) {
      $error = "The opening brace of a class, interface, trait or function must be on the same line as the declaration (K&R style).";
      $fix = $phpcs_file->addFixableError($error, $curly_brace, 'OpeningBraceNotSameLine');
      if ($fix) {
        $phpcs_file->fixer->beginChangeset();
        // Remove all whitespace and newlines between declaration and brace
        for ($i = $curly_brace - 1; $i > $last_content; $i--) {
          if ($tokens[$i]['code'] === T_WHITESPACE) {
            $phpcs_file->fixer->replaceToken($i, '');
          }
        }
        // Ensure proper spacing between declaration and brace
        if ($tokens[$last_content]['code'] !== T_WHITESPACE) {
          $phpcs_file->fixer->addContent($last_content, ' ');
        }
        $phpcs_file->fixer->endChangeset();
      }
    }
  }

}
