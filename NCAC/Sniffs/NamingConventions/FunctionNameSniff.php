<?php

namespace NCAC\Sniffs\NamingConventions;

use NCAC\Utils\StringCaseHelper;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * NCAC Coding Standard - FunctionNameSniff
 *
 * Enforces snake_case naming convention for global functions:
 * - Applies only to global functions (not methods within classes/traits)
 * - Follows Drupal naming conventions for consistency
 * - Provides automatic fixing by converting function names to snake_case
 * - Uses StringCaseHelper utility for reliable case conversion
 * - Distinguishes between global functions and class methods
 *
 * Examples of transformations:
 *   function myFunctionName()     → function my_function_name()
 *   function calculateTotalSum()  → function calculate_total_sum()
 *   function XMLParser()          → function xml_parser()
 *
 * Note: This sniff only affects global functions. Class methods are handled
 * by a separate sniff with different naming conventions.
 *
 * @category CodingStandard
 * @package  NCAC\Sniffs\NamingConventions
 * @author   NCAC
 * @license  MIT
 * @link     https://github.com/ncac-php/standard
 */
class FunctionNameSniff implements Sniff {

  /**
   * Registers the tokens this sniff wants to listen for.
   *
   * This sniff processes T_FUNCTION tokens to analyze function declarations
   * and apply snake_case naming conventions to global functions only.
   *
   * @return array<int, int> List of token codes this sniff listens to.
   */
  public function register(): array {
    // Listen for all function declarations
    return [T_FUNCTION];
  }

  /**
   * Processes function declarations to enforce snake_case naming for global functions.
   *
   * This method analyzes function declarations and applies snake_case naming rules
   * specifically to global functions (excluding class methods and trait functions).
   * It uses the StringCaseHelper utility to perform reliable case detection and
   * conversion, ensuring consistent naming across the codebase.
   *
   * @param  File $phpcs_file    The PHP_CodeSniffer file being analyzed.
   * @param  int  $stack_pointer The position of the T_FUNCTION token in the stack.
   */
  public function process(File $phpcs_file, $stack_pointer) {
    $tokens = $phpcs_file->getTokens();
    
    // Step 1: Locate the function name token following the T_FUNCTION keyword.
    $function_name_pointer = $phpcs_file->findNext(T_STRING, $stack_pointer);
    $string_case_helper = StringCaseHelper::me();
    
    if ($function_name_pointer === false) {
      // Defensive programming: malformed function declaration
      return;
    }
    
    $function_name = $tokens[$function_name_pointer]['content'];

    // Step 2: Determine if this function is global or a class/trait method.
    // We only apply snake_case rules to global functions, not methods.
    $in_class = $phpcs_file->getCondition($stack_pointer, T_CLASS) !== false;
    $in_anon_class = $phpcs_file->getCondition($stack_pointer, T_ANON_CLASS) !== false;
    $in_trait = $phpcs_file->getCondition($stack_pointer, T_TRAIT) !== false;
    $in_class_or_trait = $in_class || $in_anon_class || $in_trait;
    
    if ($in_class_or_trait) {
      // Skip methods and trait functions - they have different naming rules
      return;
    }

    // Step 3: Validate and fix function name casing if necessary.
    // Use StringCaseHelper for reliable case detection and conversion.
    if (!$string_case_helper->isSnakeCase($function_name)) {
      $fix = $phpcs_file->addFixableError(
        "Function name '$function_name' must be in snake_case (Drupal convention).",
        $function_name_pointer,
        'NotSnakeCase'
      );
      if ($fix) {
        // Apply automatic fix by converting to proper snake_case
        $phpcs_file->fixer->replaceToken($function_name_pointer, $string_case_helper->toSnakeCase($function_name));
      }
    }
  }

}
