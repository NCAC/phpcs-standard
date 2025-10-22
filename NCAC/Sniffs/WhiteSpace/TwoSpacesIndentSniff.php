<?php declare(strict_types = 1);

namespace NCAC\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * NCAC Coding Standard - TwoSpacesIndentSniff
 *
 * Enforces consistent two-space indentation throughout the codebase:
 * - Calculates expected indentation based on nesting levels (braces, brackets, parentheses)
 * - Applies 2 spaces per indentation level consistently
 * - Handles complex cases: switch/case blocks, multi-line arrays, method chaining
 * - Provides automatic fixing by normalizing indentation to exact spacing
 * - Processes entire file at once for comprehensive indentation analysis
 *
 * Special handling includes:
 * - Switch/case statement indentation rules
 * - Multi-line array and function parameter alignment
 * - Parenthesis and bracket nesting calculations
 * - Tab-to-space conversion for consistency
 *
 * @category CodingStandard
 * @package  NCAC\Sniffs\WhiteSpace
 * @author   NCAC
 * @license  MIT
 * @link     https://github.com/ncac-php/standard
 */
class TwoSpacesIndentSniff implements Sniff {

  /**
   * Registers the tokens this sniff wants to listen for.
   *
   * This sniff processes T_WHITESPACE tokens to analyze and fix indentation
   * across the entire file. It runs at the final token to ensure comprehensive
   * analysis after all other tokens have been processed.
   *
   * @return array<int, int> List of token codes this sniff listens to.
   */
  public function register(): array {
    return [T_WHITESPACE];
  }

  /**
   * Processes the entire file to analyze and fix indentation.
   *
   * This method performs comprehensive indentation analysis by:
   * 1. Collecting token codes for each line to understand structure
   * 2. Computing expected indentation levels based on nesting
   * 3. Comparing actual vs expected indentation and applying fixes
   * 4. Handling special cases like tabs, empty lines, and complex structures
   *
   * The method runs only on the final token to ensure all file content
   * is available for analysis, preventing conflicts with other sniffs.
   *
   * @param  File $phpcs_file    The PHP_CodeSniffer file being analyzed.
   * @param  int  $stack_pointer The position of the current token in the stack.
   */
  public function process(File $phpcs_file, $stack_pointer) {
    $tokens = $phpcs_file->getTokens();

    // Process only at the last token to ensure complete file analysis
    if ($stack_pointer !== count($phpcs_file->getTokens()) - 1) {
      return;
    }

    $line_token_codes = $this->collectLineTokenCodes($phpcs_file);
    $line_levels_stack = $this->computeLineLevels($line_token_codes);

    // Validate and fix indentation for each line
    $lines_seen = [];
    foreach ($tokens as $pointer => $token) {
      $line = $token['line'];

      if (isset($lines_seen[$line])) {
        continue;
      }
      $lines_seen[$line] = true;

      $line_tokens = [];
      for ($scan_pointer = $pointer; $scan_pointer < count($tokens) && $tokens[$scan_pointer]['line'] === $line; $scan_pointer++) {
        $line_tokens[] = $tokens[$scan_pointer];
      }
      
      // Find first meaningful token on the line
      $first_code_token = null;
      foreach ($line_tokens as $tok) {
        if ($tok['code'] !== T_WHITESPACE && $tok['code'] !== T_INLINE_HTML) {
          $first_code_token = $tok;
          break;
        }
      }
      
      if ($first_code_token === null) {
        continue;
      }

      // Calculate expected vs actual indentation
      $expected = ($line_levels_stack[$line] ?? 0) * 2;
      $expected = intval($expected);
      if (empty($line_tokens)) {
        continue;
      }
      
      $first_token = $line_tokens[0];
      if ($first_token['code'] === T_WHITESPACE
        || $first_token['code'] === T_DOC_COMMENT_WHITESPACE
      ) {
        $whitespace = (string) $first_token['content'];
        $actual = strlen(str_replace("\t", "  ", $whitespace)); // Convert tabs to 2 spaces
        if ($actual != $expected) {
          $fix = $phpcs_file->addFixableError(
            "Incorrect indentation: expected $expected spaces, found $actual.",
            $pointer,
            'IndentationTwoSpaces'
          );
          if ($fix) {
            $phpcs_file->fixer->beginChangeset();
            $phpcs_file->fixer->replaceToken($pointer, str_repeat(' ', $expected));
            $phpcs_file->fixer->endChangeset();
          }
        }
      } else {
        // Handle lines that should be indented but start with code
        if ($expected !== 0) {
          $fix = $phpcs_file->addFixableError(
            "Incorrect indentation: expected $expected spaces, found 0.",
            $pointer,
            'IndentationTwoSpaces'
          );
          if ($fix) {
            $phpcs_file->fixer->beginChangeset();
            $phpcs_file->fixer->addContentBefore($pointer, str_repeat(' ', $expected));
            $phpcs_file->fixer->endChangeset();
          }
        }
      }
    }
  }

  /**
   * Builds the table of token codes per line for indentation analysis.
   *
   * Iterates through all tokens to collect the sequence of meaningful tokens
   * for each line, excluding leading whitespace but preserving token order.
   * This data structure is used by computeLineLevels() to determine nesting.
   *
   * @param File $phpcs_file The file being analyzed.
   *
   * @return array<int, array<int|string>>
   *   Map of line numbers to arrays of token codes.
   *   Token codes can be native PHPCS integers or wrapper strings.
   *
   *   Example:
   *     [
   *       10 => [T_DOC_COMMENT_STAR, T_STRING],
   *       11 => [T_OPEN_CURLY_BRACKET, T_VARIABLE],
   *     ]
   */
  private function collectLineTokenCodes(File $phpcs_file): array {
    $tokens = $phpcs_file->getTokens();
    $line_token_codes = [];

    $tokens_list = array_values($tokens);
    $tokens_count = count($tokens_list);
    
    for ($pointer = 0; $pointer < $tokens_count; $pointer++) {
      $token = $tokens_list[$pointer];
      $line = $token['line'];
      
      $is_new_line = ($pointer === 0) || ($tokens_list[$pointer - 1]['line'] !== $line);
      if (!$is_new_line) {
        continue;
      }
      
      // Collect meaningful tokens for this line
      $line_tokens = [];
      $first_non_ws_found = false;
      for ($scan_pointer = $pointer; $scan_pointer < $tokens_count && $tokens_list[$scan_pointer]['line'] === $line; $scan_pointer++) {
        $code = $tokens_list[$scan_pointer]['code'];
        if (!$first_non_ws_found && ($code === T_WHITESPACE || $code === T_DOC_COMMENT_WHITESPACE || $code === T_INLINE_HTML)) {
          continue;
        }
        $first_non_ws_found = true;
        $line_tokens[] = $code;
      }
      
      // Remove trailing whitespace to simplify pattern matching
      if (!empty($line_tokens) && ($line_tokens[count($line_tokens) - 1] === T_WHITESPACE || $line_tokens[count($line_tokens) - 1] === T_DOC_COMMENT_WHITESPACE)) {
        array_pop($line_tokens);
      }
      $line_token_codes[$line] = $line_tokens;
    }
    return $line_token_codes;
  }

  private function getActualLevel(array $stack): int {
    $level = 0;
    foreach ($stack as $block) {
      if (is_string($block) && strpos($block, 'MERGED:') === 0) {
        // MERGED tokens count as 1 level (not 2)
        $level++;
      } else if ($block === 'SWITCH_CASE') {
        // SWITCH_CASE is a special marker that adds indentation for case content
        $level++;
      } else {
        $level++;
      }
    }
    return $level;
  }

  /**
   * Computes the expected indentation level for each line based on nesting structure.
   *
   * Analyzes token patterns to determine proper indentation levels using a stack-based
   * approach. Handles various PHP constructs including:
   * - DocBlocks (0.5 level = 1 space offset)
   * - Switch/case statements with consecutive case handling
   * - Ternary operators (?:)
   * - Multiline assignments
   * - Nested blocks (braces, brackets, parentheses)
   * - Arrays passed as function arguments (special handling)
   *
   * @param array<int, array<int|string>> $line_token_codes Token codes per line.
   *
   * @return array<int, float|int> Indentation levels per line (multiplied by 2 for spaces).
   */
  private function computeLineLevels(array $line_token_codes): array {
    
    $line_levels_stack = [];
    $blocks_stack = [];
    $docblock_indentation_level = 0;
    $opening_closure_tokens = [T_OPEN_CURLY_BRACKET, T_OPEN_PARENTHESIS, T_OPEN_SQUARE_BRACKET, T_OPEN_SHORT_ARRAY];

    foreach ($line_token_codes as $index_line => $line_tokens) {
      $first_token_code = $line_tokens[0] ?? null;
      $last_token_code = !empty($line_tokens) ? $line_tokens[count($line_tokens) - 1] : null;
      $start_index = 0;

      // === DocBlock Processing ===
      if ($first_token_code === T_DOC_COMMENT_OPEN_TAG) {
        $line_levels_stack[$index_line] = $this->getActualLevel($blocks_stack);
        $docblock_indentation_level = $this->getActualLevel($blocks_stack) + 0.5;
        continue;
      }

      if ($first_token_code === T_DOC_COMMENT_STAR || $first_token_code === T_DOC_COMMENT_CLOSE_TAG) {
        $line_levels_stack[$index_line] = $docblock_indentation_level;
        continue;
      }

      // === Block Structure Processing ===
      
      // Handle closing tokens - pop stack before calculating indentation
      $last_block = end($blocks_stack);
      if (
        ($first_token_code === T_CLOSE_CURLY_BRACKET && $last_block === T_OPEN_CURLY_BRACKET)
        || ($first_token_code === T_CLOSE_PARENTHESIS && $last_block === T_OPEN_PARENTHESIS)
        || ($first_token_code === T_CLOSE_SQUARE_BRACKET && $last_block === T_OPEN_SQUARE_BRACKET)
        || ($first_token_code === T_CLOSE_SHORT_ARRAY && $last_block === T_OPEN_SHORT_ARRAY)
        // Handle merged token closures  
        || ($first_token_code === T_CLOSE_SQUARE_BRACKET && is_string($last_block) && strpos($last_block, 'MERGED:') === 0 && strpos($last_block, ':' . T_OPEN_SQUARE_BRACKET) !== false)
        || ($first_token_code === T_CLOSE_SHORT_ARRAY && is_string($last_block) && strpos($last_block, 'MERGED:') === 0 && strpos($last_block, ':' . T_OPEN_SHORT_ARRAY) !== false)
        || ($first_token_code === T_CLOSE_PARENTHESIS && is_string($last_block) && strpos($last_block, 'MERGED:') === 0 && strpos($last_block, ':' . T_OPEN_PARENTHESIS) !== false)
      ) {
        array_pop($blocks_stack);
        $line_levels_stack[$index_line] = $this->getActualLevel($blocks_stack);
        $start_index = 1;

        // Handle ternary operators
      } else if ($first_token_code === T_INLINE_THEN) {
        $blocks_stack[] = T_INLINE_THEN;
        $line_levels_stack[$index_line] = $this->getActualLevel($blocks_stack);
        $start_index = 1;
      } else if ($first_token_code === T_INLINE_ELSE && end($blocks_stack) === T_INLINE_THEN) {
        $line_levels_stack[$index_line] = $this->getActualLevel($blocks_stack);
        array_pop($blocks_stack);
        $start_index = 1;      // Handle switch/case statements with consecutive case support
      } else if ($first_token_code === T_CASE || $first_token_code === T_DEFAULT) {
        // Consecutive case/default statements maintain same indentation level
        $case_level = $this->getActualLevel($blocks_stack);
        if (end($blocks_stack) === 'SWITCH_CASE') {
          $case_level = $this->getActualLevel($blocks_stack) - 1;
        }
        $line_levels_stack[$index_line] = $case_level;
        
        // Track switch case block only if not already present
        if (end($blocks_stack) !== 'SWITCH_CASE') {
          $blocks_stack[] = 'SWITCH_CASE';
        }
        $start_index = 1;

        // Handle case/default block terminators
      } else if (
        end($blocks_stack) === 'SWITCH_CASE'
        && (
          $first_token_code === T_BREAK
          || $first_token_code === T_RETURN
          || $first_token_code === T_THROW
          || $first_token_code === T_CONTINUE
          || $first_token_code === T_EXIT
          || $first_token_code === T_GOTO
          || $first_token_code === T_CLOSE_CURLY_BRACKET
        )
      ) {
        $line_levels_stack[$index_line] = $this->getActualLevel($blocks_stack);
        array_pop($blocks_stack);
        $start_index = 1;

        // Default indentation level
      } else {
        $line_levels_stack[$index_line] = $this->getActualLevel($blocks_stack);
      }

      // === Multiline Assignment Handling ===
      if ($last_token_code === T_EQUAL) {
        $blocks_stack[] = T_EQUAL;
      }
      if ($last_token_code === T_SEMICOLON && end($blocks_stack) === T_EQUAL) {
        $line_levels_stack[$index_line] = count($blocks_stack);
        array_pop($blocks_stack);
      }
      // === Process remaining tokens on the line ===
      while ($start_index < count($line_tokens)) {
        $token_code = $line_tokens[$start_index];
        if (in_array($token_code, $opening_closure_tokens)) {
          // Special handling for arrays as function arguments
          // Check if this array token immediately follows a parenthesis in the same line
          // BUT exclude switch/case contexts where normal indentation is preferred
          $in_switch_case = in_array('SWITCH_CASE', $blocks_stack, true);
          
          if (($token_code === T_OPEN_SQUARE_BRACKET || $token_code === T_OPEN_SHORT_ARRAY) 
            && !empty($blocks_stack) 
            && end($blocks_stack) === T_OPEN_PARENTHESIS
            && !$in_switch_case) {
            // Check if the parenthesis was added on this same line (meaning func([ pattern)
            $prev_token_index = $start_index - 1;
            if ($prev_token_index >= 0 && 
              ($line_tokens[$prev_token_index] === T_OPEN_PARENTHESIS)) {
              // This is an array directly after opening parentheses: func([
              // Mark this as a merged opening - store both tokens but count as one level
              array_pop($blocks_stack); // Remove the parenthesis
              $blocks_stack[] = 'MERGED:' . T_OPEN_PARENTHESIS . ':' . $token_code;
            } else {
              $blocks_stack[] = $token_code;
            }
          } else {
            // Regular opening token (parentheses, braces, arrays not in function calls)
            $blocks_stack[] = $token_code;
          }
        } else if (
          ($token_code === T_CLOSE_CURLY_BRACKET && end($blocks_stack) === T_OPEN_CURLY_BRACKET)
          || ($token_code === T_CLOSE_PARENTHESIS && end($blocks_stack) === T_OPEN_PARENTHESIS)
          || ($token_code === T_CLOSE_SQUARE_BRACKET && end($blocks_stack) === T_OPEN_SQUARE_BRACKET)
          || ($token_code === T_CLOSE_SHORT_ARRAY && end($blocks_stack) === T_OPEN_SHORT_ARRAY)
        ) {
          array_pop($blocks_stack);
        } else {
          // Handle merged token closures - only specific array-in-function cases
          $last_block = end($blocks_stack);
          if (
            ($token_code === T_CLOSE_SQUARE_BRACKET && is_string($last_block) && strpos($last_block, 'MERGED:' . T_OPEN_PARENTHESIS . ':' . T_OPEN_SQUARE_BRACKET) === 0)
            || ($token_code === T_CLOSE_SHORT_ARRAY && is_string($last_block) && strpos($last_block, 'MERGED:' . T_OPEN_PARENTHESIS . ':' . T_OPEN_SHORT_ARRAY) === 0)
          ) {
            array_pop($blocks_stack);
          }
        }
        $start_index += 1;
      }
    }

    return $line_levels_stack;
  }

}
