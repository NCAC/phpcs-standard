<?php

namespace NCAC\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * NCAC Coding Standard - PascalCaseClassNameSniff
 *
 * Enforces PascalCase naming convention for class-like structures:
 * - Applies to class, interface, and trait declarations
 * - Requires names to start with uppercase letter followed by alphanumeric characters
 * - Provides automatic fixing by converting names to proper PascalCase
 * - Handles underscore-separated names by capitalizing each segment
 * - Ensures consistent naming across all class-like declarations
 *
 * Examples of transformations:
 *   class my_class_name        → class MyClassName
 *   interface user_interface   → interface UserInterface  
 *   trait helper_trait         → trait HelperTrait
 *   class XMLParser           → class XMLParser (already valid)
 *
 * @category CodingStandard
 * @package  NCAC\Sniffs\NamingConventions
 * @author   NCAC
 * @license  MIT
 * @link     https://github.com/ncac-php/standard
 */
class PascalCaseClassNameSniff implements Sniff {

  /**
   * Registers the tokens this sniff wants to listen for.
   *
   * This sniff processes class, interface, and trait declarations to enforce
   * PascalCase naming conventions consistently across all class-like structures.
   *
   * @return array<int, int> List of token codes this sniff listens to.
   */
  public function register(): array {
    // Listen for class, interface, and trait declarations
    return [T_CLASS, T_INTERFACE, T_TRAIT];
  }

  /**
   * Processes class-like declarations to enforce PascalCase naming.
   *
   * This method analyzes class, interface, and trait names to ensure they follow
   * PascalCase conventions. It validates that names start with an uppercase letter
   * and contain only alphanumeric characters, applying automatic fixes when
   * violations are detected.
   *
   * @param  File $phpcs_file The PHP_CodeSniffer file being analyzed.
   * @param  int  $stack_ptr  The position of the T_CLASS/T_INTERFACE/T_TRAIT token.
   */
  public function process(File $phpcs_file, $stack_ptr): void {
    $tokens = $phpcs_file->getTokens();
    // Step 1: Locate the name token following the declaration keyword.
    $name_ptr = $phpcs_file->findNext(T_STRING, $stack_ptr);
    if ($name_ptr === false) {
      // Defensive programming: malformed declaration without name
      return;
    }
    /**
     * @var string $name 
     */
    $name = $tokens[$name_ptr]['content'];
    // Step 2: Validate PascalCase format using regex pattern.
    // PascalCase requires: starts with uppercase, followed by alphanumeric only.
    if (!preg_match('/^[A-Z][A-Za-z0-9]*$/', $name)) {
      $error = "Class/interface/trait name '$name' must be in PascalCase.";
      $fix = $phpcs_file->addFixableError($error, $stack_ptr, 'NotPascalCase');
      if ($fix) {
        // Step 3: Convert to PascalCase by capitalizing segments separated by underscores.
        $pascal = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
        $phpcs_file->fixer->replaceToken($name_ptr, $pascal);
      }
    }
  }

}
