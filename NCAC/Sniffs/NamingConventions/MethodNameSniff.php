<?php

namespace NCAC\Sniffs\NamingConventions;

use NCAC\Utils\StringCaseHelper;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * NCAC Coding Standard - MethodNameSniff
 *
 * Enforces camelCase naming convention for class and trait methods:
 * - Applies only to methods within classes and traits (not global functions)
 * - Excludes PHP magic methods (methods starting with __)
 * - Provides comprehensive automatic fixing of both declarations and calls
 * - Updates method calls throughout the class/trait ($this->, self::, static::)
 * - Uses StringCaseHelper utility for reliable case conversion
 *
 * Examples of transformations:
 *   function my_method_name()     → function myMethodName()
 *   function calculate_total()    → function calculateTotal()
 *   function XML_parser()         → function xmlParser()
 *
 * Method call transformations:
 *   $this->old_method()          → $this->oldMethod()
 *   self::static_method()        → self::staticMethod()
 *   static::another_method()     → static::anotherMethod()
 *
 * @category CodingStandard
 * @package  NCAC\Sniffs\NamingConventions
 * @author   NCAC
 * @license  MIT
 * @link     https://github.com/ncac-php/standard
 */
class MethodNameSniff implements Sniff {

  /**
   * Registers the tokens this sniff wants to listen for.
   *
   * This sniff processes T_FUNCTION tokens to analyze method declarations
   * within classes and traits, applying camelCase naming conventions.
   *
   * @return array<int|string> List of token codes this sniff listens to.
   */
  public function register(): array {
    // Listen for all function declarations
    return [T_FUNCTION];
  }

  /**
   * Processes function declarations to enforce camelCase naming for class methods.
   *
   * This method analyzes function declarations within classes and traits, applying
   * camelCase naming rules while excluding PHP magic methods. When violations are
   * detected, it fixes both the method declaration and all calls to that method
   * throughout the class or trait.
   *
   * @param  File $phpcs_file    The PHP_CodeSniffer file being analyzed.
   * @param  int  $stack_pointer The position of the T_FUNCTION token in the stack.
   */
  public function process(File $phpcs_file, $stack_pointer) {
    $tokens = $phpcs_file->getTokens();
    
    // Step 1: Locate the method name token following the T_FUNCTION keyword.
    $function_name_pointer = $phpcs_file->findNext(T_STRING, $stack_pointer);
    $string_case_helper = StringCaseHelper::me();
    
    if ($function_name_pointer === false) {
      // Defensive programming: malformed function declaration
      return;
    }
    
    $function_name = $tokens[$function_name_pointer]['content'];

    // Step 2: Verify this is a method within a class or trait context.
    // Global functions are handled by a separate sniff with different rules.
    $in_class_or_trait = $phpcs_file->getCondition($stack_pointer, T_CLASS) !== false
    || $phpcs_file->getCondition($stack_pointer, T_TRAIT) !== false;
      
    if (!$in_class_or_trait) {
      // Not a method - skip global functions
      return;
    }

    // Step 3: Exclude PHP magic methods from camelCase enforcement.
    // Magic methods have standardized names that shouldn't be modified.
    if (preg_match('/^__/', $function_name)) {
      return;
    }

    // Step 4: Validate and fix method name casing if necessary.
    // Apply comprehensive fixes to both declaration and all method calls.
    if (!$string_case_helper->isCamelCase($function_name)) {
      $camel = $string_case_helper->toCamelCase($function_name);
      $fix = $phpcs_file->addFixableError(
        "Method name '$function_name' must be in camelCase.",
        $function_name_pointer,
        'NotCamelCase'
      );
      if ($fix) {
        // Fix the method declaration itself
        $phpcs_file->fixer->replaceToken($function_name_pointer, $camel);
        // Fix all calls to this method throughout the class/trait
        $this->fixMethodCalls($phpcs_file, $function_name, $camel);
      }
    }
  }

  /**
   * Fixes all method calls throughout the class/trait when a method is renamed.
   *
   * This private method performs a comprehensive search and replace operation
   * to update all references to a renamed method. It handles various call patterns:
   * - Instance method calls via $this->methodName()
   * - Static method calls via self::methodName() and static::methodName()
   * 
   * The method ensures consistency by updating all call sites when the
   * declaration is renamed, preventing broken references.
   *
   * @param  File   $phpcs_file The PHP_CodeSniffer file being processed.
   * @param  string $old_name   The original method name to be replaced.
   * @param  string $new_name   The new camelCase method name.
   */
  private function fixMethodCalls(File $phpcs_file, string $old_name, string $new_name): void {
    $tokens = $phpcs_file->getTokens();
    
    foreach ($tokens as $ptr => $token) {
      // Ensure pointer is an integer for safe array access
      if (!is_int($ptr)) {
        continue;
      }
      
      // Handle instance method calls: $this->oldMethodName()
      if (
        $token['code'] === T_OBJECT_OPERATOR 
        && isset($tokens[$ptr + 1]) 
        && $tokens[$ptr + 1]['code'] === T_STRING 
        && $tokens[$ptr + 1]['content'] === $old_name
      ) {
        $phpcs_file->fixer->replaceToken($ptr + 1, $new_name);
      }
      
      // Handle static method calls: self::oldMethodName() or static::oldMethodName()
      if (
        $token['code'] === T_DOUBLE_COLON 
        && isset($tokens[$ptr + 1]) 
        && $tokens[$ptr + 1]['code'] === T_STRING 
        && $tokens[$ptr + 1]['content'] === $old_name
      ) {
        $phpcs_file->fixer->replaceToken($ptr + 1, $new_name);
      }
    }
  }

}
