<?php

namespace NCAC\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * NCAC Coding Standard - DeclarationSpacingSniff
 *
 * Enforces exactly one space between declaration keywords and identifiers:
 * - function <name>() - exactly 1 space after 'function'
 * - class <name> - exactly 1 space after 'class'
 * - interface <name> - exactly 1 space after 'interface'
 * - trait <name> - exactly 1 space after 'trait'
 * - enum <name> - exactly 1 space after 'enum'
 *
 * Examples of violations:
 *   function   my_function() - TOO MANY spaces (3)
 *   class    MyClass - TOO MANY spaces (4)
 *   trait  MyTrait - TOO MANY spaces (2)
 *
 * Examples of correct formatting:
 *   function my_function() - CORRECT (1 space)
 *   class MyClass - CORRECT (1 space)
 *   trait MyTrait - CORRECT (1 space)
 *
 * @category CodingStandard
 * @package  NCAC\Sniffs\WhiteSpace
 * @author   NCAC
 * @license  MIT
 * @link     https://github.com/ncac-php/standard
 */
class DeclarationSpacingSniff implements Sniff {

  /**
   * Registers the tokens this sniff wants to listen for.
   *
   * @return array<int, int> List of token codes this sniff listens to.
   */
  public function register(): array {
    return [
      T_FUNCTION,
      T_CLASS,
      T_INTERFACE,
      T_TRAIT,
      T_ENUM,
    ];
  }

  /**
   * Processes declaration tokens to enforce single space after keyword.
   *
   * @param File $phpcs_file The PHP_CodeSniffer file being analyzed.
   * @param int $stack_pointer The position of the current token in the stack.
   */
  public function process(File $phpcs_file, $stack_pointer) {
    $tokens = $phpcs_file->getTokens();
    $keyword_token = $tokens[$stack_pointer];

    // Find the next non-whitespace token (the identifier)
    $next_non_whitespace = $phpcs_file->findNext(T_WHITESPACE, $stack_pointer + 1, null, true);

    if ($next_non_whitespace === false) {
      // No identifier found (malformed code)
      return;
    }

    // Check if there's whitespace between keyword and identifier
    if ($next_non_whitespace === $stack_pointer + 1) {
      // No space at all - this is also an error
      $error = sprintf(
        'Expected 1 space after %s keyword; 0 found',
        strtoupper($keyword_token['content'])
      );
      $fix = $phpcs_file->addFixableError($error, $stack_pointer, 'NoSpace');
      if ($fix) {
        $phpcs_file->fixer->addContent($stack_pointer, ' ');
      }
      return;
    }

    // There is whitespace - check if it's exactly 1 space
    $whitespace_token = $tokens[$stack_pointer + 1];

    if ($whitespace_token['code'] !== T_WHITESPACE) {
      // No whitespace token found (shouldn't happen, but defensive programming)
      return;
    }

    $space_count = strlen($whitespace_token['content']);

    if ($space_count !== 1) {
      $error = sprintf(
        'Expected 1 space after %s keyword; %d found',
        strtoupper($keyword_token['content']),
        $space_count
      );
      $fix = $phpcs_file->addFixableError($error, $stack_pointer + 1, 'TooManySpaces');

      if ($fix) {
        // Replace multiple spaces with exactly one space
        $phpcs_file->fixer->replaceToken($stack_pointer + 1, ' ');
      }
    }
  }

}
