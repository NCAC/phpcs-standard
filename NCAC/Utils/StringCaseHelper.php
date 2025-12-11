<?php

namespace NCAC\Utils;

/**
 * Utility class for string case conversions and checks.
 *
 * Provides methods to check and convert between camelCase, PascalCase, snake_case, and SNAKE_UPPER_CASE.
 * Used by NCAC sniffs to enforce naming conventions and provide automatic fixes.
 *
 * @package NCAC\Utils
 */
class StringCaseHelper {

  /**
   * Singleton instance of StringCaseHelper.
   *
   */
  private static ?StringCaseHelper $instance = null;

  /**
   * Returns the singleton instance of StringCaseHelper.
   *
   */
  public static function me(): StringCaseHelper {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Checks if a string is in camelCase (or all lowercase).
   *
   */
  public function isCamelCase(string $string): bool {
    // If the string is all lowercase, it's valid camelCase
    if (ctype_lower($string)) {
      return true;
    }
    // Must not start with uppercase, must have at least one uppercase, no double uppercase, not end with uppercase
    return (
      !ctype_upper($string[0])
      && !$this->repeatsUppercaseChars($string)
      && $this->hasUpperChars($string)
      && !ctype_upper(substr($string, strlen($string) - 1))
    );
  }

  /**
   * Checks if a string is in PascalCase.
   *
   */
  public function isPascalCase(string $string): bool {
    // Must start with uppercase, have at least one lowercase, no double uppercase, not end with uppercase
    return (
      ctype_upper($string[0])
      && !$this->repeatsUppercaseChars($string)
      && $this->hasLowerChars($string)
      && !ctype_upper(substr($string, strlen($string) - 1))
    );
  }

  /**
   * Checks if a string is in SNAKE_UPPER_CASE (uppercase, digits, underscores).
   *
   */
  public function isSnakeUpperCase(string $string): bool {
    // Must contain only uppercase letters, digits, or underscores, and at least one letter
    if (!preg_match('/^[A-Z0-9_]+$/', $string)) {
      return false;
    }
    // No double underscores
    if (strpos($string, '__') !== false) {
      return false;
    }
    // No leading/trailing underscore
    if ($string[0] === '_' || substr($string, -1) === '_') {
      return false;
    }
    // Must contain at least one uppercase letter
    if (!preg_match('/[A-Z]/', $string)) {
      return false;
    }
    return true;
  }

  /**
   * Checks if a string is in snake_case (lowercase, digits, underscores).
   *
   * @param string $string The string to check.
   * @param bool $allow_double_underscore Allow double underscores (__) in the string.
   * @param bool $allow_leading_underscore Allow leading underscore (_) in the string.
   * @return bool True if the string is in snake_case, false otherwise.
   */
  public function isSnakeCase(string $string, bool $allow_double_underscore = false, bool $allow_leading_underscore = false): bool {
    // If the string is all lowercase, it's valid snake_case
    if (ctype_lower($string)) {
      return true;
    }
    // Must contain only lowercase, digits, or underscores, and at least one letter
    if (!preg_match('/^[a-z0-9_]+$/', $string)) {
      return false;
    }
    // Check double underscores if not allowed
    if (!$allow_double_underscore && strpos($string, '__') !== false) {
      return false;
    }
    // Check leading underscore if not allowed
    if (!$allow_leading_underscore && $string[0] === '_') {
      return false;
    }
    // No trailing underscore
    if (substr($string, -1) === '_') {
      return false;
    }
    // Must contain at least one lowercase letter
    if (!preg_match('/[a-z]/', $string)) {
      return false;
    }
    return true;
  }

  /**
   * Converts a string to camelCase (e.g. my_variable → myVariable).
   *
   */
  public function toCamelCase(string $string): string {
    // Convert underscores to lowercase (snake_case)
    $camel_case_string = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    // Replace double underscores with single
    $camel_case_string = preg_replace('/_+/', '_', $camel_case_string);
    // Remove underscores and capitalize the next letter
    $camel_case_string = preg_replace_callback(
      '/_([a-z])/',
      function ($matches) {
        return strtoupper($matches[1]);
      },
      $camel_case_string
    );
    return $camel_case_string;
  }

  /**
   * Converts a string to snake_case (e.g. MyVariable → my_variable).
   *
   * @param string $string The string to convert.
   * @param bool $allow_double_underscore Preserve double underscores (__) in the string.
   * @param bool $allow_leading_underscore Preserve leading underscore (_) in the string.
   * @return string The converted snake_case string.
   */
  public function toSnakeCase(string $string, bool $allow_double_underscore = false, bool $allow_leading_underscore = false): string {
    // Preserve leading underscore if allowed
    $leading_underscore = '';
    if ($allow_leading_underscore && $string[0] === '_') {
      $leading_underscore = '_';
      $string = substr($string, 1);
    }

    // Insert underscore before each uppercase (except at the start)
    $string = preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $string);
    // Convert to lowercase
    $string = strtolower($string);

    // Handle double underscores
    if (!$allow_double_underscore) {
      // Replace double underscores with single
      $string = preg_replace('/_+/', '_', $string);
    } else {
      // Only replace 3+ underscores with double underscores
      $string = preg_replace('/_{3,}/', '__', $string);
    }

    // Remove trailing underscores
    $string = rtrim($string, '_');

    // Remove leading underscores only if not allowed
    if (!$allow_leading_underscore) {
      $string = ltrim($string, '_');
    }

    return $leading_underscore . $string;
  }

  /**
   * Converts a string to PascalCase (e.g. my_variable → MyVariable).
   *
   */
  public function toPascalCase(string $string): string {
    // Convert to snake_case first
    $pascal_case_string = $this->toSnakeCase($string);
    // Capitalize the first letter of each word and remove underscores
    $pascal_case_string = str_replace('_', '', ucwords($pascal_case_string, '_'));
    return $pascal_case_string;
  }

  /**
   * Converts a string to SNAKE_UPPER_CASE (e.g. my_variable → MY_VARIABLE).
   *
   */
  public function toSnakeUpperCase(string $string): string {
    // Convert to snake_case then uppercase
    $snake_case_string = $this->toSnakeCase($string);
    return strtoupper($snake_case_string);
  }

  /**
   * Detects the presence of two consecutive uppercase letters in the string.
   *
   */
  private function repeatsUppercaseChars(string $string): bool {
    $found_uppercase_last = false;
    foreach (str_split($string) as $character) {
      if (ctype_upper($character)) {
        if ($found_uppercase_last) {
          return true;
        }
        $found_uppercase_last = true;
      } else {
        $found_uppercase_last = false;
      }
    }
    return false;
  }

  /**
   * Checks for at least one uppercase letter in the string.
   *
   */
  private function hasUpperChars(string $string): bool {
    return preg_match('/[A-Z]/', $string) === 1;
  }

  /**
   * Checks for at least one lowercase letter in the string.
   *
   */
  private function hasLowerChars(string $string): bool {
    return preg_match('/[a-z]/', $string) === 1;
  }

}
