<?php declare(strict_types=1);

namespace NCAC\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * NCAC Coding Standard - NoAlternateControlStructureSniff
 *
 * Detects and reports alternate control structure syntax violations:
 * - Forbids if:/endif, foreach:/endforeach, while:/endwhile patterns
 * - Requires standard curly brace syntax for all control structures
 * - Detection-only sniff (no automatic fixing to avoid conflicts with other sniffs)
 * - Handles complex nested structures and edge cases safely
 * - Supports if/elseif/else chains, loops, switches, and declare statements
 *
 * Examples of forbidden syntax that will be reported:
 *   if ($condition): ... endif;        ✗ Use if ($condition) { ... }
 *   foreach ($arr as $item): ... endforeach; ✗ Use foreach ($arr as $item) { ... }
 *   while ($condition): ... endwhile;  ✗ Use while ($condition) { ... }
 *
 * ## Why This Sniff is Detection-Only
 *
 * This sniff intentionally provides NO automatic fixes (via PHPCBF) due to
 * technical limitations in PHP_CodeSniffer's architecture:
 *
 * 1. **Token Regeneration Issue**: When sniffs modify tokens (e.g., using
 *    `addContentBefore()` or `replaceToken()`), subsequent sniffs in the
 *    processing queue may not see the updated token stream.
 *
 * 2. **Execution Order Conflicts**: Sniffs like `TwoSpacesIndentSniff` run
 *    after this sniff and can overwrite token modifications, leading to
 *    invalid PHP syntax (e.g., missing closing braces).
 *
 * 3. **Complex Transformation Requirements**: Converting alternate syntax
 *    (especially if/elseif/else chains) requires sophisticated token
 *    manipulation that conflicts with whitespace normalization sniffs.
 *
 * ## Recommended Workflow
 *
 * For automatic correction of alternate control structures:
 *
 * ```bash
 * # 1. Use PHP-CS-Fixer for corrections (coming in future NCAC releases)
 * php-cs-fixer fix --rules=@NCAC-ControlStructures src/
 *
 * # 2. Validate with PHPCS
 * phpcs --standard=NCAC src/
 * ```
 *
 * This approach ensures clean, conflict-free corrections while maintaining
 * the validation capabilities of PHPCS.
 *
 * @category CodingStandard
 * @package  NCAC\Sniffs\Formatting
 * @author   NCAC
 * @license  MIT
 * @link     https://github.com/ncac-php/standard
 * @see      https://github.com/squizlabs/PHP_CodeSniffer/issues/3932 Token regeneration limitations
 * @since    1.0.0 Detection-only implementation
 * @todo     Create complementary PHP-CS-Fixer rules for automatic correction
 */
class NoAlternateControlStructureSniff implements Sniff {

  /**
   * Registers the tokens this sniff wants to listen for.
   *
   * This sniff targets both opening and closing tokens of alternate control structures:
   * - Opening tokens (if, elseif, else, foreach, etc.) to catch colon syntax (:)
   * - Closing tokens (endif, endforeach, etc.) to detect and fix alternate syntax usage.
   *
   * @return array<int, int> List of token codes this sniff listens to.
   */
  public function register(): array {
    return [
      // Opening tokens that can use alternate syntax
      \T_FOREACH,
      \T_WHILE,
      \T_SWITCH,
      \T_DECLARE,
      \T_FOR,
      // Closing tokens of alternate structures
      \T_ENDIF,
      \T_ENDFOREACH,
      \T_ENDWHILE,
      \T_ENDSWITCH,
      \T_ENDDECLARE,
      \T_ENDFOR
    ];
  }

  /**
   * Processes alternate control structure tokens for detection only.
   *
   * This method detects alternate control structure syntax violations without
   * applying any automatic fixes. It processes different types of structures:
   * - Opening tokens (foreach, while, for, switch, declare) that use colon syntax
   * - Closing tokens (endif, endforeach, etc.) for complete violation detection
   * - Complex if/elseif/else chains via processIfChain()
   *
   * All violations are reported as non-fixable errors with guidance to use
   * PHP-CS-Fixer for automatic correction.
   *
   * @param  File $phpcs_file    The PHP_CodeSniffer file being analyzed.
   * @param  int  $stack_pointer The position of the alternate structure token.
   */
  public function process(File $phpcs_file, int $stack_pointer) {
    $tokens = $phpcs_file->getTokens();
    $token = $tokens[$stack_pointer];

    // Special handling for IF chains (if/elseif/else/endif)
    if ($token['code'] === \T_ENDIF) {
      $this->processIfChain($phpcs_file, $stack_pointer);
      return;
    }

    // Handle simple opening tokens that might use alternate syntax (colon)
    if (\in_array($token['code'], [\T_FOREACH, \T_WHILE, \T_FOR, \T_SWITCH, \T_DECLARE], true)) {
      // Look for a colon after this token (indicating alternate syntax)
      $colon = $phpcs_file->findNext([T_COLON], $stack_pointer + 1, null, false, null, true);
      if ($colon !== false) {
        // Make sure this colon belongs to our structure and not something else (like ternary)
        // We need to skip over parentheses content for structures like for() loops
        $has_intervening_structure = false;
        $paren_level = 0;
        for ($i = $stack_pointer + 1; $i < $colon; $i++) {
          if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
            $paren_level++;
          } else if ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
            $paren_level--;
          } else if ($paren_level === 0 && \in_array($tokens[$i]['code'], [T_SEMICOLON, T_OPEN_CURLY_BRACKET], true)) {
            $has_intervening_structure = true;
            break;
          }
        }
        if (!$has_intervening_structure) {
          $phpcs_file->addError(
            'Alternate control structure syntax is forbidden: use curly braces {}. Use PHP-CS-Fixer for automatic correction.',
            $stack_pointer,
            'NoAlternateControlStructure'
          );
        }
      }
      return;
    }

    // Handle closing tokens for simple structures
    if (\in_array($token['code'], [\T_ENDFOREACH, \T_ENDWHILE, \T_ENDFOR, \T_ENDSWITCH, \T_ENDDECLARE], true)) {
      // Map closing tokens to their corresponding opening tokens
      $structure_map = [
        \T_ENDFOREACH => \T_FOREACH,
        \T_ENDWHILE => \T_WHILE,
        \T_ENDFOR => \T_FOR,
        \T_ENDSWITCH => \T_SWITCH,
        \T_ENDDECLARE => \T_DECLARE,
      ];
      // Find the corresponding opening token
      $open_token_type = $structure_map[$token['code']];
      $open_pointer = $token['scope_condition'] ?? null;
      if ($open_pointer !== null && $tokens[$open_pointer]['code'] === $open_token_type) {
        // Check if the opening structure uses colon syntax
        $colon = $phpcs_file->findNext([T_COLON], $open_pointer + 1, $stack_pointer);
        if ($colon !== false) {
          // Report error for the opening token (detection only)
          $phpcs_file->addError(
            'Alternate control structure syntax is forbidden: use curly braces {}. Use PHP-CS-Fixer for automatic correction.',
            $open_pointer,
            'NoAlternateControlStructure'
          );
        }
      }
      // Handle the closing token (detection only)
      $phpcs_file->addError(
        'Alternate control structure syntax is forbidden: use curly braces {}. Use PHP-CS-Fixer for automatic correction.',
        $stack_pointer,
        'NoAlternateControlStructure'
      );
      return;
    }
  }

  /**
   * Processes an entire if/elseif/else/endif chain for detection only.
   *
   * Complex if/elseif/else chains require sophisticated fixing that conflicts
   * with other sniffs (especially TwoSpacesIndentSniff). This method only
   * reports errors for detection purposes. Use PHP-CS-Fixer for automatic
   * correction of complex alternate syntax structures.
   *
   * @param File $phpcs_file    The PHP_CodeSniffer file being analyzed.
   * @param int  $endif_pointer The position of the endif token.
   */
  private function processIfChain(File $phpcs_file, int $endif_pointer): void {
    $tokens = $phpcs_file->getTokens();

    // Find the matching T_IF by walking backwards through nested structures
    $open_pointer = null;
    $level = 0;
    for ($i = $endif_pointer - 1; $i >= 0; $i--) {
      if ($tokens[$i]['code'] === \T_ENDIF) {
        $level++;
      }
      if ($tokens[$i]['code'] === \T_IF) {
        if ($level === 0) {
          $open_pointer = $i;
          break;
        } else {
          $level--;
        }
      }
    }
    if ($open_pointer === null) {
      return; // Could not find matching T_IF
    }

    // Collect all if/elseif/else tokens in this chain
    $chain_tokens = [];
    for ($i = $open_pointer; $i < $endif_pointer; $i++) {
      if (\in_array($tokens[$i]['code'], [\T_IF, \T_ELSEIF, \T_ELSE], true)) {
        // Look for colon after this control structure token
        $colon = $phpcs_file->findNext(T_COLON, $i + 1, $endif_pointer);
        if ($colon !== false) {
          $chain_tokens[] = [
            'token' => $i,
            'colon' => $colon,
            'type' => $tokens[$i]['code']
          ];
        }
      }
    }

    // Only process if we found alternate syntax tokens
    if (empty($chain_tokens)) {
      return;
    }

    // Report errors for detection only (no auto-fixing for any alternate syntax)
    foreach ($chain_tokens as $chain_token) {
      $phpcs_file->addError(
        'Alternate control structure syntax is forbidden: use curly braces {}. Use PHP-CS-Fixer for automatic correction.',
        $chain_token['token'],
        'NoAlternateControlStructure'
      );
    }

    // Report error for the endif token (detection only)
    $phpcs_file->addError(
      'Alternate control structure syntax is forbidden: use curly braces {}. Use PHP-CS-Fixer for automatic correction.',
      $endif_pointer,
      'NoAlternateControlStructure'
    );
  }

}
