<?php declare(strict_types=1);

namespace NCAC\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;


/**
 * NCAC Coding Standard - NoAlternateControlStructureSniff
 *
 * Enforces the prohibition of alternate control structure syntax:
 * - Forbids if:/endif, foreach:/endforeach, while:/endwhile patterns
 * - Requires standard curly brace syntax for all control structures
 * - Provides automatic fixing to convert alternate syntax to standard braces
 * - Handles complex nested structures and edge cases safely
 * - Supports if/elseif/else chains, loops, switches, and declare statements
 *
 * Examples of forbidden syntax that will be auto-fixed:
 *   if ($condition): ... endif;        → if ($condition) { ... }
 *   foreach ($arr as $item): ... endforeach; → foreach ($arr as $item) { ... }
 *   while ($condition): ... endwhile;  → while ($condition) { ... }
 *
 * @category CodingStandard
 * @package  NCAC\Sniffs\Formatting
 * @author   NCAC
 * @license  MIT
 * @link     https://github.com/ncac-php/standard
 */
class NoAlternateControlStructureSniff implements Sniff {

  /**
   * Registers the tokens this sniff wants to listen for.
   *
   * This sniff targets the closing tokens of alternate control structures
   * (endif, endforeach, etc.) to detect and fix alternate syntax usage.
   * By focusing on closing tokens, we can work backwards to find the
   * corresponding opening structures and apply comprehensive fixes.
   *
   * @return array<int, int> List of token codes this sniff listens to.
   */
  public function register(): array {
    // Only register alternate structure closing tokens (endif, endforeach, etc.)
    return [
      T_ENDIF,
      T_ENDFOREACH,
      T_ENDWHILE,
      T_ENDSWITCH,
      T_ENDDECLARE,
      T_ENDFOR
    ];
  }

  /**
   * Processes alternate control structure tokens and applies fixes.
   *
   * This method handles the conversion of alternate control structure syntax
   * to standard curly brace syntax. It processes different types of structures:
   * - if/elseif/else/endif chains (with special nested handling)
   * - Simple structures like foreach/endforeach, while/endwhile, etc.
   * 
   * The method works backwards from closing tokens to find opening structures
   * and applies comprehensive fixes including colon-to-brace conversion and
   * semicolon removal.
   *
   * @param  File $phpcs_file    The PHP_CodeSniffer file being analyzed.
   * @param  int  $stack_pointer The position of the closing alternate structure token.
   */
  public function process(File $phpcs_file, $stack_pointer) {
    $tokens = $phpcs_file->getTokens();
    $token = $tokens[$stack_pointer];

    // Special handling for if/elseif/else/endif blocks due to their complexity.
    // These structures can have multiple intermediate tokens (elseif, else) that
    // need individual processing before handling the final endif.
    if ($token['code'] === T_ENDIF) {
      // Robustly find the matching T_IF by walking backwards through nested structures.
      // We maintain a level counter to handle properly nested if statements.
      $open_pointer = null;
      $level = 0;
      for ($i = $stack_pointer - 1; $i >= 0; $i--) {
        if ($tokens[$i]['code'] === T_ENDIF) {
          $level++;
        }
        if ($tokens[$i]['code'] === T_IF) {
          if ($level === 0) {
            $open_pointer = $i;
            break;
          } else {
            $level--;
          }
        }
      }
      if ($open_pointer === null) {
        // Defensive programming: could not find the matching opening T_IF
        return;
      }
      
      // Process each if/elseif/else that uses colon syntax within this block
      for ($i = $open_pointer; $i < $stack_pointer; $i++) {
        if (in_array($tokens[$i]['code'], [T_IF, T_ELSEIF, T_ELSE], true)) {
          $colon = $phpcs_file->findNext(T_COLON, $i + 1, $stack_pointer);
          if ($colon !== false && $colon < $stack_pointer) {
            $fix = $phpcs_file->addFixableError(
              'Alternate control structure syntax is forbidden: use curly braces {}.',
              $colon,
              'NoAlternateControlStructure'
            );
            if ($fix) {
              // Convert colon to opening curly brace
              $phpcs_file->fixer->replaceToken($colon, '{');
            }
          }
        }
      }
      // Process the endif token itself
      $fix = $phpcs_file->addFixableError(
        'Alternate control structure syntax is forbidden: use curly braces {}.',
        $stack_pointer,
        'NoAlternateControlStructure'
      );
      if ($fix) {
        // Replace endif with closing curly brace
        $phpcs_file->fixer->replaceToken($stack_pointer, '}');
        // Clean up trailing semicolon that's no longer needed
        $next_token = $tokens[$stack_pointer + 1] ?? null;
        if ($next_token !== null && $next_token['code'] === T_SEMICOLON) {
          $phpcs_file->fixer->replaceToken($stack_pointer + 1, '');
        }
      }
      return;
    }

    // Handle all other alternate control structures (loops, switches, declarations).
    // These have simpler structure with just opening and closing tokens.
    if (in_array($token['code'], [T_ENDFOREACH, T_ENDWHILE, T_ENDFOR, T_ENDSWITCH, T_ENDDECLARE], true)) {
      // Map closing tokens to their corresponding opening tokens
      $structure_map = [
        T_ENDFOREACH => T_FOREACH,
        T_ENDWHILE => T_WHILE,
        T_ENDFOR => T_FOR,
        T_ENDSWITCH => T_SWITCH,
        T_ENDDECLARE => T_DECLARE,
      ];
      $open_pointer = $token['scope_condition'] ?? null;
      if ($open_pointer === null) {
        // Defensive programming: could not find valid opening token
        return;
      }
      
      if (!isset($structure_map[$token['code']]) || $tokens[$open_pointer]['code'] !== $structure_map[$token['code']]) {
        // Defensive programming: invalid token mapping
        return;
      }
      
      // Process the opening structure if it uses colon syntax
      $colon = $phpcs_file->findNext(T_COLON, $open_pointer + 1, $stack_pointer);
      if ($colon !== false && $colon < $stack_pointer) {
        $fix = $phpcs_file->addFixableError(
          'Alternate control structure syntax is forbidden: use curly braces {}.',
          $open_pointer,
          'NoAlternateControlStructure'
        );
        if ($fix) {
          // Convert colon to opening curly brace
          $phpcs_file->fixer->replaceToken($colon, '{');
        }
      }
      
      // Process the closing token (endforeach, endwhile, etc.)
      $fix = $phpcs_file->addFixableError(
        'Alternate control structure syntax is forbidden: use curly braces {}.',
        $stack_pointer,
        'NoAlternateControlStructure'
      );
      if ($fix) {
        // Replace the closing token with a standard closing curly brace
        $phpcs_file->fixer->replaceToken($stack_pointer, '}');
        // Remove trailing semicolon that's no longer needed
        $next_token = $tokens[$stack_pointer + 1] ?? null;
        if ($next_token !== null && $next_token['code'] === T_SEMICOLON) {
          $phpcs_file->fixer->replaceToken($stack_pointer + 1, '');
        }
      }
      return;
    }
  }

}
