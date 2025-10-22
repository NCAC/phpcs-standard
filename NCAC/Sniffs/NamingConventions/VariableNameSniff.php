<?php

namespace NCAC\Sniffs\NamingConventions;

use NCAC\Utils\StringCaseHelper;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use const T_CLOSURE;
use const T_OPEN_PARENTHESIS;

/**
 * NCAC Coding Standard - VariableNameSniff
 *
 * Enforces context-specific variable naming conventions:
 * - Function/closure parameters: snake_case (Drupal convention)
 * - Class/trait properties: camelCase (OOP convention)
 * - Dynamic properties ($obj->$var): snake_case
 * - Local variables: snake_case
 * - Global variables: snake_case
 * - Excludes PHP superglobals from all rules
 *
 * This sniff intelligently determines variable context and applies appropriate
 * naming rules. It provides comprehensive automatic fixing while preserving
 * the semantic meaning of different variable types.
 *
 * Examples of transformations by context:
 *   function foo($MyParam)        → function foo($my_param)
 *   $this->MyProperty = 1;        → $this->myProperty = 1;
 *   $obj->$DynamicVar = 2;        → $obj->$dynamic_var = 2;
 *   $LocalVar = 3;                → $local_var = 3;
 *   global $GlobalVar;            → global $global_var;
 *
 * @category CodingStandard
 * @package  NCAC\Sniffs\NamingConventions
 * @author   NCAC
 * @license  MIT
 * @link     https://github.com/ncac-php/standard
 */
class VariableNameSniff implements Sniff {

  /**
   * Registers the tokens this sniff wants to listen for.
   *
   * This sniff processes all T_VARIABLE tokens to analyze variable usage
   * across different contexts and apply appropriate naming conventions.
   *
   * @return array<int|string> List of token codes this sniff listens to.
   */
  public function register(): array {
    // Listen for all variable tokens
    return [T_VARIABLE];
  }

  /**
   * Processes variable tokens to enforce context-specific naming conventions.
   *
   * This method performs comprehensive context analysis to determine the appropriate
   * naming convention for each variable. It sequentially checks for different variable
   * types (parameters, properties, dynamic properties, local/global variables) and
   * applies the corresponding naming rules with automatic fixing.
   *
   * @param  File $phpcs_file    The PHP_CodeSniffer file being analyzed.
   * @param  int  $stack_pointer The position of the T_VARIABLE token in the stack.
   */
  public function process(File $phpcs_file, $stack_pointer) {
    $tokens = $phpcs_file->getTokens();
    $token = $tokens[$stack_pointer];
    $var_name = ltrim($token['content'], '$');
    $string_case_utils = StringCaseHelper::me();

    // Step 1: Skip PHP superglobals - they have standardized names.
    if ($this->isSuperglobal($var_name)) {
      return;
    }

    // Step 2: Function/closure parameters must use snake_case (Drupal convention).
    if ($this->isFunctionParameter($phpcs_file, $stack_pointer)) {
      if (!$string_case_utils->isSnakeCase($var_name)) {
        $fix = $phpcs_file->addFixableError(
          "Parameter name '$var_name' must be in snake_case.",
          $stack_pointer,
          'ParamNotSnakeCase'
        );
        if ($fix) {
          $fixed = $string_case_utils->toSnakeCase($var_name);
          $phpcs_file->fixer->replaceToken($stack_pointer, '$' . $fixed);
        }
      }
      return;
    }

    // Step 3: Class/trait properties must use camelCase (OOP convention).
    if ($this->isClassProperty($phpcs_file, $stack_pointer)) {
      if (!$string_case_utils->isCamelCase($var_name)) {
        $fix = $phpcs_file->addFixableError(
          "Property name '$var_name' must be in camelCase.",
          $stack_pointer,
          'PropertyNotCamelCase'
        );
        
        if ($fix) {
          $fixed = $string_case_utils->toCamelCase($var_name);
          $phpcs_file->fixer->replaceToken($stack_pointer, '$' . $fixed);
        }
      }
      return;
    }

    // Step 4: Dynamic properties ($obj->$var) must use snake_case.
    if ($this->isDynamicProperty($phpcs_file, $stack_pointer)) {
      if (!isset($tokens[$stack_pointer]['line'])) {
        return;
      }
      if (!$string_case_utils->isSnakeCase($var_name)) {
        $fix = $phpcs_file->addFixableError(
          "Dynamic property variable '$var_name' must be in snake_case.",
          $stack_pointer,
          'DynamicPropertyVarNotSnakeCase'
        );
        if ($fix) {
          $fixed = $string_case_utils->toSnakeCase($var_name);
          $phpcs_file->fixer->replaceToken($stack_pointer, '$' . $fixed);
        }
      }
      return;
    }

    // Step 5: Local/global variables must use snake_case.
    $is_local_variable = $this->isLocalVariable($phpcs_file, $stack_pointer);
    $is_global_variable = $this->isGlobalVariable($phpcs_file, $stack_pointer);
    if ($is_local_variable || $is_global_variable) {
      if (!$string_case_utils->isSnakeCase($var_name)) {
        $fix = $phpcs_file->addFixableError(
          "Variable name '$var_name' must be in snake_case.",
          $stack_pointer,
          'LocalOrGlobalNotSnakeCase'
        );
        if ($fix) {
          $fixed = $string_case_utils->toSnakeCase($var_name);
          $phpcs_file->fixer->replaceToken($stack_pointer, '$' . $fixed);
        }
      }
      return;
    }
    // If no specific context is detected, skip processing
  }

  /**
   * Checks if the variable is a PHP superglobal.
   *
   * PHP superglobals have standardized names that should not be modified.
   * This method maintains a static list of all official PHP superglobals.
   *
   * @param  string $name Variable name without the leading '$'.
   * @return bool True if the variable is a PHP superglobal.
   */
  private function isSuperglobal(string $name): bool {
    static $superglobals = [
      '_GET',
      '_POST',
      '_SERVER',
      '_FILES',
      '_COOKIE',
      '_SESSION',
      '_ENV',
      '_REQUEST',
      'GLOBALS'
    ];
    return in_array($name, $superglobals, true);
  }

  /**
   * Checks if the variable is a function or closure parameter.
   *
   * This method searches backwards from the variable position to find function
   * or closure declarations, then verifies if the variable is within the
   * parameter list by checking parenthesis boundaries.
   *
   * @param  File $phpcs_file    The PHP_CodeSniffer file being analyzed.
   * @param  int  $stack_pointer The position of the variable token.
   * @return bool True if the variable is a function/closure parameter.
   */
  private function isFunctionParameter(File $phpcs_file, int $stack_pointer): bool {
    $tokens = $phpcs_file->getTokens();
    // Look for a function or closure declaration before the variable
    foreach ([T_FUNCTION, T_CLOSURE] as $type) {
      $function_pointer = $phpcs_file->findPrevious([$type], $stack_pointer, null, false);
      if ($function_pointer !== false) {
        $open_paren = $phpcs_file->findNext([T_OPEN_PARENTHESIS], $function_pointer, null, false);
        $close_paren = $tokens[$open_paren]['parenthesis_closer'] ?? null;
        // Check if the variable is inside the parameter list
        if ($open_paren !== false && is_int($close_paren) && $stack_pointer > $open_paren && $stack_pointer < $close_paren) {
          return true;
        }
      }
    }
    return false;
  }

  /**
   * Checks if the variable is a class or trait property.
   *
   * This method looks for visibility modifiers (public, private, protected, static, var)
   * on the same line as the variable to identify property declarations. It also
   * ensures the variable is within a class or trait context.
   *
   * @param  File $phpcs_file    The PHP_CodeSniffer file being analyzed.
   * @param  int  $stack_pointer The position of the variable token.
   * @return bool True if the variable is a class/trait property.
   */
  private function isClassProperty(File $phpcs_file, int $stack_pointer): bool {
    $tokens = $phpcs_file->getTokens();
    // Look for a visibility modifier on the same line
    $previous_pointer = $phpcs_file->findPrevious(
      [
        T_PUBLIC,
        T_PROTECTED,
        T_PRIVATE,
        T_STATIC,
        T_VAR
      ], $stack_pointer - 1, null, false
    );
    if (
      $previous_pointer !== false
      && $tokens[$previous_pointer]['line'] === $tokens[$stack_pointer]['line']
    ) {
      // if $previous_pointer matches a static modifier and the stack_pointer is inside a function, skip because it's likely a static variable inside a method
      if ($tokens[$previous_pointer]['code'] === T_STATIC) {
        $function_pointer = $phpcs_file->getCondition($stack_pointer, T_FUNCTION);
        if ($function_pointer !== false) {
          return false;
        }
      }
      // Ensure we are inside a class or trait
      $class_pointer = $phpcs_file->getCondition($stack_pointer, T_CLASS);
      $trait_pointer = $phpcs_file->getCondition($stack_pointer, T_TRAIT);
      if ($class_pointer !== false || $trait_pointer !== false) {
        return true;
      }
      return false;
    }
    return false;
  }

  /**
   * Checks if the variable is a dynamic property ($obj->$var).
   *
   * Dynamic properties are variables used as property names after the object
   * operator (->). This method checks for the object operator immediately
   * preceding the variable on the same line.
   *
   * @param  File $phpcs_file    The PHP_CodeSniffer file being analyzed.
   * @param  int  $stack_pointer The position of the variable token.
   * @return bool True if the variable is a dynamic property.
   */
  private function isDynamicProperty(File $phpcs_file, int $stack_pointer): bool {
    $tokens = $phpcs_file->getTokens();
    $previous_pointer = $phpcs_file->findPrevious([T_OBJECT_OPERATOR], $stack_pointer - 1, null, false);
    return $previous_pointer !== false && $tokens[$previous_pointer]['line'] === $tokens[$stack_pointer]['line'];
  }

  /**
   * Checks if the variable is a local variable (assignment or foreach).
   *
   * Local variables are those defined within function or closure scope.
   * This method excludes dynamic properties ($this->var) and considers
   * any other variable within a function/closure as local.
   *
   * @param  File $phpcs_file    The PHP_CodeSniffer file being analyzed.
   * @param  int  $stack_pointer The position of the variable token.
   * @return bool True if the variable is a local variable.
   */
  private function isLocalVariable(File $phpcs_file, int $stack_pointer): bool {
    $tokens = $phpcs_file->getTokens();
    $function_pointer = $phpcs_file->getCondition($stack_pointer, T_FUNCTION);
    $closure_pointer = $phpcs_file->getCondition($stack_pointer, T_CLOSURE);
    // We are inside a function or closure
    if ($function_pointer !== false || $closure_pointer !== false) {
      // Exclude dynamic properties ($this->var)
      $previous_pointer = $phpcs_file->findPrevious([T_OBJECT_OPERATOR], $stack_pointer - 1, null, false);
      if ($previous_pointer !== false && $tokens[$previous_pointer]['line'] === $tokens[$stack_pointer]['line']) {
        return false;
      }
      // Any other variable in a function is local
      return true;
    }
    return false;
  }

  /**
   * Checks if the variable is a global variable (declared outside any function/method).
   *
   * Global variables are those defined in the global scope, outside of any
   * function, closure, or class context. This method checks that the variable
   * is not within any structured code block.
   *
   * @param  File $phpcs_file    The PHP_CodeSniffer file being analyzed.
   * @param  int  $stack_pointer The position of the variable token.
   * @return bool True if the variable is a global variable.
   */
  private function isGlobalVariable(File $phpcs_file, int $stack_pointer): bool {
    $function_pointer = $phpcs_file->getCondition($stack_pointer, T_FUNCTION);
    $closure_pointer = $phpcs_file->getCondition($stack_pointer, T_CLOSURE);
    $class_pointer = $phpcs_file->getCondition($stack_pointer, T_CLASS);
    // Not in a function, closure or class => global variable
    return $function_pointer === false && $closure_pointer === false && $class_pointer === false;
  }

}
