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
    // Listen for whitespace tokens (indentation is only relevant for whitespace)
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
    // Retrieve all tokens from the file
    $tokens = $phpcs_file->getTokens();

    // Only run this sniff at the last token to avoid conflicts with other sniffs
    if ($stack_pointer !== count($phpcs_file->getTokens()) - 1) {
      return;
    }

    // Step 1: Collect token codes for each line to understand code structure
    $line_token_codes = $this->collectLineTokenCodes($phpcs_file);

    // Step 2: Compute expected indentation levels based on nesting analysis
    $line_levels_stack = $this->computeLineLevels($line_token_codes);

    // Step 3: Validate and fix actual indentation against expected levels
    $lines_seen = [];
    foreach ($tokens as $pointer => $token) {
      $line = $token['line'];

      // Only process the first token of each line to avoid duplicate processing
      if (isset($lines_seen[$line])) {
        continue;
      }
      $lines_seen[$line] = true;

      // Collect all tokens for the current line for analysis
      $line_tokens = [];
      for ($scan_pointer = $pointer; $scan_pointer < count($tokens) && $tokens[$scan_pointer]['line'] === $line; $scan_pointer++) {
        $line_tokens[] = $tokens[$scan_pointer];
      }
      
      // Find the first meaningful (non-whitespace) token on the line
      $first_code_token = null;
      foreach ($line_tokens as $tok) {
        if ($tok['code'] !== T_WHITESPACE && $tok['code'] !== T_INLINE_HTML) {
          $first_code_token = $tok;
          break;
        }
      }
      
      // Skip lines that contain only whitespace or are empty
      if ($first_code_token === null) {
        continue;
      }

      // Step 4: Calculate expected indentation and compare with actual
      $expected = ($line_levels_stack[$line] ?? 0) * 2;
      $expected = intval($expected);
      if (empty($line_tokens)) {
        continue; // Avoid error if the line contains no tokens
      }
      
      $first_token = $line_tokens[0];
      // Process lines that start with whitespace tokens
      if ($first_token['code'] === T_WHITESPACE
        || $first_token['code'] === T_DOC_COMMENT_WHITESPACE
      ) {
        $whitespace = (string) $first_token['content'];
        // Convert tabs to spaces for consistent measurement (1 tab = 2 spaces)
        $actual = strlen(str_replace("\t", "  ", $whitespace));
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
        // Handle lines that should be indented but start with non-whitespace
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
   * @param File $phpcs_file
   *   The file being analyzed.
   *
   * @return array<int, array<int|string>>
   *   Associative array: keys are line numbers, values are arrays of token codes (either native PHPCS int or string, e.g. 'PHPCS_T_*' injected by a wrapper).
   *   PHPCS guarantees that strict comparison with T_* constants works for both types.
   *
   *   Example:
   *     [
   *       10 => [T_WHITESPACE, T_DOC_COMMENT_STAR, 'PHPCS_T_INLINE_THEN'],
   *       11 => [T_OPEN_CURLY_BRACKET, T_VARIABLE],
   *     ]
   */
  private function collectLineTokenCodes(File $phpcs_file): array {
    $tokens = $phpcs_file->getTokens();
    $line_token_codes = [];

    // Iterate over all tokens to determine expected indentation for each line
    $tokens_list = array_values($tokens); // Ensure numeric keys for safe arithmetic
    $tokens_count = count($tokens_list);
    for ($pointer = 0; $pointer < $tokens_count; $pointer++) {
      $token = $tokens_list[$pointer];
      $line = $token['line'];
      // Detect new line: first token or line number changes
      $is_new_line = ($pointer === 0) || ($tokens_list[$pointer - 1]['line'] !== $line);
      if (!$is_new_line) {
        continue; // Skip to next token if not a new line
      }
      // For each line, store an array of token codes starting with the first non-whitespace token.
      $line_tokens = [];
      $first_non_ws_found = false;
      for ($scan_pointer = $pointer; $scan_pointer < $tokens_count && $tokens_list[$scan_pointer]['line'] === $line; $scan_pointer++) {
        $code = $tokens_list[$scan_pointer]['code'];
        if (!$first_non_ws_found && ($code === T_WHITESPACE || $code === T_DOC_COMMENT_WHITESPACE || $code === T_INLINE_HTML)) {
          continue;
        }
        $first_non_ws_found = true;
        // Store code as-is (int or string), no conversion needed.
        $line_tokens[] = $code;
      }
      // Remove the last token if it is whitespace (to ease closure pattern matching)
      if (!empty($line_tokens) && ($line_tokens[count($line_tokens) - 1] === T_WHITESPACE || $line_tokens[count($line_tokens) - 1] === T_DOC_COMMENT_WHITESPACE)) {
        array_pop($line_tokens);
      }
      $line_token_codes[$line] = $line_tokens;
    }
    return $line_token_codes;
  }

  /**
   * Computes the expected indentation level for each line, based on token structure.
   *
   * @param array<int, array<int|string>> $line_token_codes
   *   Associative array: keys are line numbers, values are arrays of token codes (int or string, e.g. native PHPCS or 'PHPCS_T_*' from wrappers).
   *   See collectLineTokenCodes() for details and examples.
   *
   * @return array<int, float|int>
   *   Associative array: keys are line numbers, values are the expected indentation level (float, e.g. 0, 0.5, 1, ...).
   *   The level is used to compute the number of spaces (level * 2) for each line.
   *
   *   Example:
   *     [
   *       10 => 0,    // Top-level line
   *       11 => 0.5,  // Docblock line (1 space)
   *       12 => 1,    // Nested block (2 spaces)
   *     ]
   */
  private function computeLineLevels(array $line_token_codes): array {
    
    $line_levels_stack = [];
    $blocks_stack = []; // Stack to track opened blocks (curly braces, brackets, parentheses)

    // new tracking variables
    $docblock_indentation_level = 0;

    // utility variables
    $opening_closure_tokens = [T_OPEN_CURLY_BRACKET, T_OPEN_PARENTHESIS, T_OPEN_SQUARE_BRACKET, T_OPEN_SHORT_ARRAY];


    foreach ($line_token_codes as $index_line => $line_tokens) {

      $first_token_code = $line_tokens[0] ?? null;
      $last_token_code = !empty($line_tokens) ? $line_tokens[count($line_tokens) - 1] : null;

      $start_index = 0;


      // --- DocBlock logic, outside indentation stack logic
      // Case 1: Docblock opening tag.
      // Example:
      // /**
      //  * // -> this line
      //  */
      // The docblock opening line stays at the parent level (column 0).
      // The following lines (stars and content) are indented at +0.5 level (1 space) relative to the parent block.
      // This is achieved by incrementing the current_level by 0.5 for the next lines.
      if ($first_token_code === T_DOC_COMMENT_OPEN_TAG) {
        $line_levels_stack[$index_line] = count($blocks_stack); // we take the current stack
        $docblock_indentation_level = count($blocks_stack) + 0.5;
        continue;
      }

      // Case 2: Line inside a docBlock that begins with a "*" or "*/"
      // Example:
      //   /**
      //    * Some description // -> this line
      //    * @param string $foo // -> and this line
      //    */ => this line too
      // All lines inside a docblock (starting with "*") are already indented at +0.5 level (1 space) relative to the parent block (see T_DOC_COMMENT_OPEN_TAG)
      if ($first_token_code === T_DOC_COMMENT_STAR
        || $first_token_code === T_DOC_COMMENT_CLOSE_TAG
      ) {
        $line_levels_stack[$index_line] = $docblock_indentation_level;
        continue;
      }


      // --- Indentation stack logic ---
      
      if (// If the line starts with a closing token (e.g. '}', ']', ')', or array close),
        // we must pop the stack BEFORE calculating the indentation level for this line.
        // This ensures that lines like '} else {' or a simple '}' are aligned at the parent block level.
        // The $start_index is set to 1 to skip the closing token for the rest of the stack logic.
        ($first_token_code === T_CLOSE_CURLY_BRACKET && end($blocks_stack) === T_OPEN_CURLY_BRACKET)
        || ($first_token_code === T_CLOSE_PARENTHESIS && end($blocks_stack) === T_OPEN_PARENTHESIS)
        || ($first_token_code === T_CLOSE_SQUARE_BRACKET && end($blocks_stack) === T_OPEN_SQUARE_BRACKET)
        || ($first_token_code === T_CLOSE_SHORT_ARRAY && end($blocks_stack) === T_OPEN_SHORT_ARRAY)
      ) {
        array_pop($blocks_stack);
        // Indentation level is calculated after popping, so this line is at the parent block level.
        $line_levels_stack[$index_line] = count($blocks_stack);
        $start_index = 1;
      } else if (// If the line starts with a T_INLINE_THEN (?)
        // we pust push a T_INLINE_THEN to the stack to track the ternary level
        // The indentation level is calculated after pushing, so this line is indented one level deeper.
        // The $start_index is set to 1 to skip the T_INLINE_THEN for the rest of the stack logic.
        $first_token_code === T_INLINE_THEN
      ) {
        $blocks_stack[] = T_INLINE_THEN;
        $line_levels_stack[$index_line] = count($blocks_stack);
        $start_index = 1;
      } else if (// It the line starts with a T_INLINE_ELSE (:) and the last opened block is a T_INLINE_THEN
        // The indentation level is calculated before popping, so this line is at the same level as the T_INLINE_THEN
        // we pop the T_INLINE_THEN and do not push a T_INLINE_ELSE to the stack
        // The $start_index is set to 1 to skip the T_INLINE_ELSE for the rest of the stack logic.
        $first_token_code === T_INLINE_ELSE && end($blocks_stack) === T_INLINE_THEN
      ) {
        $line_levels_stack[$index_line] = count($blocks_stack);
        array_pop($blocks_stack);
        $start_index = 1;
      } else if (// Si la ligne commence par un T_CASE ou T_DEFAULT
        $first_token_code === T_CASE || $first_token_code === T_DEFAULT
      ) {
        // Indentation au niveau courant
        $line_levels_stack[$index_line] = count($blocks_stack);
        // On marque l'imbrication d'un case/default dans la stack
        $blocks_stack[] = 'SWITCH_CASE';
        $start_index = 1;
      } else if (// Gestion de la sortie d'un bloc case/default imbriqué
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
        // Indentation avant de dépiler
        $line_levels_stack[$index_line] = count($blocks_stack);
        array_pop($blocks_stack);
        $start_index = 1;
      } else {
        // If the line does not start with a closing token, the indentation level is simply the current stack depth.
        $line_levels_stack[$index_line] = count($blocks_stack);
      }

      // --- Multiline assignment indentation logic ---
      // If the last token of the line is T_EQUAL (=), push it to the stack to track multiline assignment.
      if ($last_token_code === T_EQUAL) {
        $blocks_stack[] = T_EQUAL;
      }
      // If the last token of the line is T_SEMICOLON and the last stack element is T_EQUAL, pop T_EQUAL.
      if ($last_token_code === T_SEMICOLON && end($blocks_stack) === T_EQUAL) {
        // Indentation level for this line is before popping T_EQUAL.
        $line_levels_stack[$index_line] = count($blocks_stack);
        array_pop($blocks_stack);
      }

      // 2.
      while ($start_index < count($line_tokens)) {
        $token_code = $line_tokens[$start_index];
        if (in_array($token_code, $opening_closure_tokens)) {
          $blocks_stack[] = $token_code;
        } else if (
          ($token_code === T_CLOSE_CURLY_BRACKET && end($blocks_stack) === T_OPEN_CURLY_BRACKET)
          || ($token_code === T_CLOSE_PARENTHESIS && end($blocks_stack) === T_OPEN_PARENTHESIS)
          || ($token_code === T_CLOSE_SQUARE_BRACKET && end($blocks_stack) === T_OPEN_SQUARE_BRACKET)
          || ($token_code === T_CLOSE_SHORT_ARRAY && end($blocks_stack) === T_OPEN_SHORT_ARRAY)
        ) {
          array_pop($blocks_stack);
        }
        $start_index += 1;
      }
    }

    return $line_levels_stack;
  }

}
