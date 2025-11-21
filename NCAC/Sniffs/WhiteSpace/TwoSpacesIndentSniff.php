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

  public const SPACES = 2;

  // ==========================================
  // CONTEXT TYPES FOR INDENTATION STACK
  // ==========================================

  /**
   * Block: code section delimited by { } (function, class, if, while, for, etc.)
   */
  public const BLOCK = 'BLOCK';

  /**
   * Switch block: switch { } statement
   */
  public const SWITCH_BLOCK = 'SWITCH_BLOCK';

  /**
   * Case block: case/default ... : section
   */
  public const CASE_BLOCK = 'CASE_BLOCK';

  /**
   * Multiline list: array/function call delimited by ( ) or [ ]
   */
  public const LIST_MULTILINE = 'LIST_MULTILINE';

  /**
   * Multiline assignment: assignment spanning multiple lines
   */
  public const ASSIGNATION_MULTILINE = 'ASSIGNATION_MULTILINE';

  /**
   * Ternary operator: ternary expression ? :
   */
  public const TERNARY_OPERATOR = 'TERNARY_OPERATOR';

  /**
   * Match expression: PHP 8+ match { } expression
   */
  public const MATCH_EXPRESSION = 'MATCH_EXPRESSION';

  /**
   * Chained method: method chaining with -> or ?->
   */
  public const CHAINED_BLOCK = 'CHAINED_BLOCK'; 


  /**
   * @var array<int|string> Tokens that open a multiline list.
   */
  public const LIST_MULTILINE_OPEN_TOKENS = [
    T_OPEN_PARENTHESIS,
    T_OPEN_SQUARE_BRACKET,
    T_OPEN_SHORT_ARRAY
  ];

  /**
   * @var array<int|string> Tokens that close a multiline list.
   */
  public const LIST_MULTILINE_CLOSE_TOKENS = [
    T_CLOSE_PARENTHESIS,
    T_CLOSE_SQUARE_BRACKET,
    T_CLOSE_SHORT_ARRAY,
  ];

  /**
   * Stack of indentation contexts (blocks, lists, chains, etc.)
   * 
   * @var array<int, self::BLOCK|self::SWITCH_BLOCK|self::CASE_BLOCK|self::LIST_MULTILINE|self::ASSIGNATION_MULTILINE|self::TERNARY_OPERATOR|self::MATCH_EXPRESSION|self::CHAINED_BLOCK>
   */
  private array $blockStack = [];

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

    // Process only at the last token to ensure complete file analysis
    if ($stack_pointer !== count($phpcs_file->getTokens()) - 1) {
      return;
    }

    // CRITICAL: Reset the stack for each file
    // The sniff instance is reused across multiple files in PHPUnit
    $this->blockStack = [];

    $line_token_codes = $this->collectLineTokenCodes($phpcs_file);


    foreach ($line_token_codes as $line_number => $line_info) {
      $this->processLine($line_number, $line_info, $phpcs_file);
      $actual_indent = $line_info['actual_indent'];
      $expected_indent = $line_info['expected_indent'];
      if (
        $expected_indent !== null
        && $actual_indent !== $expected_indent
        && !($line_info['tokens'] === ['HEREDOC_BYPASS'])
      ) {
        $this->reportIndentationError($phpcs_file, $line_info, $expected_indent, $actual_indent, $line_number);
      }
    }


  }

  /**
   * Builds the table of token codes per line for indentation analysis.
   *
   * Iterates through all tokens to collect the sequence of meaningful tokens
   * for each line, excluding leading whitespace but preserving token order.
   * This structure is used by processLine() to determine nesting depth.
   *
   * @param File $phpcs_file The file being analyzed.
   *
   * @return array<int, array{tokens: array<int, string|int>, actual_indent: int, expected_indent: int|null}>
   *   Map of line numbers to associative arrays:
   *     - tokens: list of significant token codes
   *     - actual_indent: actual indentation (spaces)
   *     - expected_indent: expected indentation (or null if not applicable)
   */
  private function collectLineTokenCodes(File $phpcs_file): array {
    // Get all tokens from the file
    $tokens = $phpcs_file->getTokens();
    $line_token_codes = [];

    $tokens_list = array_values($tokens);
    $tokens_count = count($tokens_list);
    // Iterate over all tokens to group them by line
    for ($pointer = 0; $pointer < $tokens_count; $pointer++) {
      $token = $tokens_list[$pointer];
      $line = $token['line'];
      // Detect new line start
      $is_new_line = ($pointer === 0) || ($tokens_list[$pointer - 1]['line'] !== $line);
      if (!$is_new_line) {
        continue;
      }
      // Collect significant tokens for this line
      $actual_indent = 0;
      $line_tokens = [];
      $first_non_ws_found = false;
      $first_significant_token_ptr = null; // To track pointer to first token
      for ($scan_pointer = $pointer; $scan_pointer < $tokens_count && $tokens_list[$scan_pointer]['line'] === $line; $scan_pointer++) {
        $code = $tokens_list[$scan_pointer]['code'];

        // replace content 'match' with T_MATCH
        $token_content = $tokens_list[$scan_pointer]['content'] ?? '';
        if (strtolower($token_content) === 'match') { // single remap for T_MATCH
          $code = T_MATCH;
        }

        // Count leading whitespace (excluding newlines)
        if (!$first_non_ws_found && ($code === T_WHITESPACE || $code === T_DOC_COMMENT_WHITESPACE)) {
          $content = $tokens_list[$scan_pointer]['content'];
          // Skip newlines - they're not indentation
          if ($content !== "\n" && $content !== "\r\n" && $content !== "\r") {
            $actual_indent += strlen($content);
          }
          continue;
        }
        $first_non_ws_found = true;
        // Save pointer to first significant token
        if ($first_significant_token_ptr === null) {
          $first_significant_token_ptr = $scan_pointer;
        }
        $line_tokens[] = $code;
      }
      // Remove trailing whitespace tokens for pattern matching
      if (
        !empty($line_tokens)
        && (
          $line_tokens[count($line_tokens) - 1] === T_WHITESPACE
          || $line_tokens[count($line_tokens) - 1] === T_DOC_COMMENT_WHITESPACE
        )
      ) {
        array_pop($line_tokens);
      }
      // Check for special cases BEFORE filtering comments
      $first_token_code_unfiltered = $line_tokens[0] ?? null;
      // Special handling for single line comments (//, #, /* ... */) - check BEFORE filtering
      if ($first_token_code_unfiltered === T_COMMENT && $first_significant_token_ptr !== null) {
        $first_token_content = $tokens_list[$first_significant_token_ptr]['content'] ?? '';
        $trimmed_content = trim($first_token_content);
        if (strpos($trimmed_content, '//') === 0 || strpos($trimmed_content, '#') === 0) {
          $line_token_codes[$line] = [
            'tokens' => ['SINGLE_LINE_COMMENT'],
            'actual_indent' => $actual_indent,
            'expected_indent' => null
          ];
          continue;
        }
        if (strpos($trimmed_content, '/*') === 0 && strpos($trimmed_content, '*/') !== false) {
          $line_token_codes[$line] = [
            'tokens' => ['SINGLE_LINE_COMMENT'],
            'actual_indent' => $actual_indent,
            'expected_indent' => null
          ];
          continue;
        }
      }
      // Filter out all whitespace tokens and end-of-line comments for analysis
      $line_tokens = array_filter($line_tokens, function($code) {
        return $code !== T_WHITESPACE && $code !== T_DOC_COMMENT_WHITESPACE && $code !== T_COMMENT;
      });

      // reset the keys after filtering
      $line_tokens = array_values($line_tokens);

      $first_token_code = $line_tokens[0] ?? null;
      // Special handling for Heredoc/Nowdoc blocks: skip indentation
      if ($this->isInsideHeredocOrNowdoc($line, $tokens_list, $pointer)) {
        $line_token_codes[$line] = [
          'tokens' => ['HEREDOC_BYPASS'],
          'actual_indent' => $actual_indent,
          'expected_indent' => null
        ];
        continue;
      }
      // Special handling for DocBlock start
      if ($first_token_code === T_DOC_COMMENT_OPEN_TAG) {
        $line_token_codes[$line] = [
          'tokens' => ['START_DOC_BLOCK'],
          'actual_indent' => $actual_indent,
          'expected_indent' => null
        ];
        continue;
      }
      // Special handling for DocBlock continuation/end
      if (
        $first_token_code === T_DOC_COMMENT_STAR
        || $first_token_code === T_DOC_COMMENT_CLOSE_TAG
      ) {
        $line_token_codes[$line] = [
          'tokens' => ['DOC_BLOCK_LINE'],
          'actual_indent' => $actual_indent,
          'expected_indent' => null
        ];
        continue;
      }
      // Blank line handling: if no non-whitespace token found, mark sentinel for later fix (Prettier style => 0 indent)
      if (!$first_non_ws_found) {
        $line_token_codes[$line] = [
          'tokens' => ['BLANK_LINE'],
          'actual_indent' => $actual_indent,
          'expected_indent' => null
        ];
        continue;
      }
      // Default: regular line, store token codes and indentation
      $line_token_codes[$line] = [
        'tokens' => $line_tokens,
        'actual_indent' => $actual_indent,
        'expected_indent' => null
      ];
    }
    return $line_token_codes;
  }

  /**
   * Handles trivial cases that don't require complex stack logic.
   * 
   * Returns true if the case was handled (and processLine should return early).
   * 
   * @param array<string, mixed> $line_info Line information including tokens and indentation
   * @return bool True if case was handled
   */
  private function handleTrivialCases(array &$line_info): bool {
    $line_tokens = $line_info['tokens'];

    // Case 1: Blank line - Prettier style = no whitespace at all
    if (
      empty($line_tokens)
      || (
        count($line_tokens) === 1
        && $line_tokens[0] === 'BLANK_LINE'
      )
    ) {
      if ($line_info['actual_indent'] > 0) {
        // Blank line should have 0 indentation (Prettier style)
        $line_info['expected_indent'] = 0;
      } else {
        // Blank line is correct (no indentation), skip validation
        $line_info['expected_indent'] = null;
      }
      return true;
    }

    // Case 2: DocBlock start, single-line comment, or whitespace-only line
    if (
      count($line_tokens) === 1
      && (
        $line_tokens[0] === T_WHITESPACE
        || $line_tokens[0] === 'START_DOC_BLOCK'
        || $line_tokens[0] === 'SINGLE_LINE_COMMENT'
      )
    ) {
      $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
      return true;
    }

    // Case 3: DocBlock continuation lines (with star)
    if (
      count($line_tokens) === 1
      && $line_tokens[0] === 'DOC_BLOCK_LINE'
    ) {
      // DocBlock lines have +1 space for the star alignment
      $line_info['expected_indent'] = (count($this->blockStack) * self::SPACES) + intdiv(self::SPACES, 2);
      return true;
    }

    // Case 4: Heredoc/Nowdoc bypass - skip indentation checks entirely
    if (
      count($line_tokens) === 1
      && $line_tokens[0] === 'HEREDOC_BYPASS'
    ) {
      // expected_indent is already null by default
      return true;
    }

    return false; // Not a trivial case, continue with complex logic
  }

  /**
   * Processes a line to identify method chaining patterns.
   * @param int $line_number 
   * @param array{tokens: array<int, string|int>, actual_indent: int, expected_indent: int|null} $line_info 
   * @param File $phpcs_file 
   * @return void 
   */
  private function processLine(int $line_number, array &$line_info, File $phpcs_file): void {
    if ($this->handleTrivialCases($line_info)) {
      return;
    }

    $line_tokens = $line_info['tokens'];

    $first_token = $line_tokens[0];
    $last_token = $line_tokens[count($line_tokens) - 1] ?? null;
    
    // Skip if we don't have valid tokens
    if ($last_token === null) {
      return;
    }

    // ==========================================
    // CHAINED_BLOCK AUTO-POP LOGIC
    // ==========================================
    // If line does NOT start with -> or ?->, and CHAINED_BLOCK is on top,
    // then we've reached the end of the chain - pop it
    if (
      !in_array($first_token, [T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR])
      && $this->getStackTop() === self::CHAINED_BLOCK
    ) {
      array_pop($this->blockStack);
    }

    $event = $this->detectEvent($line_tokens, $first_token, $last_token);

    if ($this->handleBlockEvents($event['type'], $event['data'], $line_info)) {
      return;
    }

    if ($this->handleListEvents($event['type'], $event['data'], $line_info)) {
      return;
    }

    if ($this->handleSwitchCaseEvents($event['type'], $event['data'], $line_info)) {
      return;
    }

    if ($this->handleMatchEvents($event['type'], $event['data'], $line_info, $line_number, $phpcs_file)) {
      return;
    }

    if ($this->handleMethodChainEvents($event['type'], $event['data'], $line_info)) {
      return;
    }

    if ($this->handleAssignmentEvents($event['type'], $event['data'], $line_info)) {
      return;
    }

    if ($this->handleTernaryEvents($event['type'], $event['data'], $line_info)) {
      return;
    }

    // Default case: set expected indentation based on current stack depth
    $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;

  }

  /**
   * Checks if a line is inside a Heredoc or Nowdoc block.
   *
   * @param int   $line        The line number to check.
   * @param array $tokens_list All tokens in the file.
   * @param int   $pointer     Current token pointer.
   *
   * @return bool True if the line is inside Heredoc/Nowdoc content.
   */
  private function isInsideHeredocOrNowdoc(int $line, array $tokens_list, int $pointer): bool {
    // Look backwards from current position to find Heredoc/Nowdoc markers
    for ($i = $pointer; $i >= 0; $i--) {
      $token = $tokens_list[$i];
      // If we find a Heredoc/Nowdoc start on a previous line
      if (
        ($token['code'] === T_START_HEREDOC || $token['code'] === T_START_NOWDOC) 
        && $token['line'] < $line
      ) {
        // Look forward to find the corresponding end marker
        for ($j = $i + 1; $j < count($tokens_list); $j++) {
          $end_token = $tokens_list[$j];
          if (
            ($end_token['code'] === T_END_HEREDOC || $end_token['code'] === T_END_NOWDOC)
            && $end_token['line'] >= $line
          ) {
            // We're between start and end markers
            return $end_token['line'] > $line; // True if end is after current line
          }
        }
      }
      // If we reach a line before potential Heredoc start, stop searching
      if ($token['line'] < $line - 100) { // Reasonable limit for performance
        break;
      }
    }
    return false;
  }

  /**
   * Détecte si la ligne est dans le contexte d'une expression match.
   * @param array<int, string|int> $line_tokens
   * @return bool
   */
  private function isMatchExpression(array $line_tokens): bool {
    return in_array(T_MATCH, $line_tokens, true);
  }

  /**
   * Get top element of stack.
   *
   * @return string|null
   */
  private function getStackTop(): ?string {
    return empty($this->blockStack) ? null : end($this->blockStack);
  }

  /**
   * Get Parent element of stack.
   * @return string|null 
   */
  private function getStackParent(): ?string {
    $count = count($this->blockStack);
    if ($count < 2) {
      return null;
    }
    $top = array_pop($this->blockStack);
    $parent = end($this->blockStack);
    $this->blockStack[] = $top; // restore top
    return $parent;
  }

  /**
   * Pop element from stack if it matches expected type.
   *
   * @param string $expected_type
   * @return bool
   */
  private function popIfTop(string $expected_type): bool {
    if ($this->getStackTop() === $expected_type) {
      array_pop($this->blockStack);
      return true;
    }
    return false;
  }

  /**
   * Push element to stack if not already on top.
   *
   * @param 'BLOCK'|'LIST_MULTILINE'|'TERNARY_OPERATOR'|'CHAINED_BLOCK'|'SWITCH_BLOCK'|'CASE_BLOCK'|'MATCH_EXPRESSION'|'ASSIGNATION_MULTILINE' $expected_type
   * @return bool
   */
  private function pushIfNotTop(string $expected_type): bool {
    if ($this->getStackTop() !== $expected_type) {
      /** @psalm-suppress PropertyTypeCoercion */
      $this->blockStack[] = $expected_type;
      return true;
    }
    return false;
  }

  /**
   * Pop tous les éléments de la stack jusqu'à matcher le type donné (inclus).
   * @param string $expected_type
   * @return void
   */
  private function popStackUntil(string $expected_type): void {
    while (!empty($this->blockStack)) {
      $type = array_pop($this->blockStack);
      if ($type === $expected_type) {
        break;
      }
    }
  }

  /**
   * Report indentation error.
   *
   * @param File $phpcs_file
   * @param array{tokens: array<int, int|string>, actual_indent: int, expected_indent: int|null, first_significant_token?: int|null, last_significant_token?: int|null, comment_type?: string|null, indent?: int} $line_info
   * @param int|null $expected
   * @param int $actual
   * @param int $line_number
   * @return void
   */
  private function reportIndentationError(File $phpcs_file, array $line_info, ?int $expected, int $actual, int $line_number): void {
    if ($expected === null) {
      return; // Cannot report an error without an expected value
    }
    $tokens = $phpcs_file->getTokens();
    // Find the first token on this line to report the error
    $token_index = null;
    foreach ($tokens as $index => $token) {
      if ($token['line'] === $line_number) {
        $token_index = $index;
        break;
      }
    }
    if ($token_index === null) {
      return; // No token found on this line
    }
    $message = sprintf(
      'Line indented incorrectly; expected %d spaces, found %d',
      $expected,
      $actual
    );
    $fix = $phpcs_file->addFixableError($message, $token_index, 'IndentationTwoSpaces');
    if ($fix) {
      $this->fixIndentation($phpcs_file, $token_index, $expected, $line_number);
    }
  }

  /**
   * Fix indentation for a line.
   *
   * @param File $phpcs_file
   * @param int $token_index
   * @param int $expected_indent
   * @param int $line_number
   * @return void
   */
  private function fixIndentation(File $phpcs_file, int $token_index, int $expected_indent, int $line_number): void {
    $tokens = $phpcs_file->getTokens();
    // Find the first whitespace token on this line
    foreach ($tokens as $index => $token) {
      if ($token['line'] === $line_number) {
        if ($token['code'] === T_WHITESPACE || $token['code'] === T_DOC_COMMENT_WHITESPACE) {
          $phpcs_file->fixer->replaceToken($index, $expected_indent === 0 ? '' : str_repeat(' ', $expected_indent));
        } else {
          if ($expected_indent > 0) {
            $phpcs_file->fixer->addContentBefore($index, str_repeat(' ', $expected_indent));
          }
        }
        break;
      }
    }
  }

  /**
   * Handles block-related events (BLOCK_OPEN, BLOCK_CLOSE).
   * 
   * @param string $event_type The type of event
   * @param array<string, mixed> $event_data Additional event data
   * @param array<string, mixed> $line_info Line information to update
   * @return bool True if event was handled
   */
  private function handleBlockEvents(string $event_type, array $event_data, array &$line_info): bool {
    switch ($event_type) {
      case 'BLOCK_CLOSE_BLOCK_OPEN':
        // Close current block, then open new block on same line (e.g., "} else {")
        $this->popStackUntil(self::BLOCK);
        // Indentation for this line is at the new depth (after close, before open)
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        // Now push new BLOCK
        $this->blockStack[] = self::BLOCK;
        return true;

      case 'CLOSURE_CLOSE_COMMA':
        // Closure closure with comma: },
        // Only pop the BLOCK, preserve LIST_MULTILINE underneath
        if ($this->getStackTop() === self::BLOCK) {
          array_pop($this->blockStack);
        }
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        return true;

      case 'BLOCK_CLOSE':
        // Pop all contexts up to and including BLOCK (cascade pop)
        // This handles cases like: if ( ... ->chain() ) { }
        $this->popStackUntil(self::BLOCK);
        // Set expected indentation at the new depth
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        return true;

      case 'CLOSURE_OPEN':
        // Closure opening: function(...) { 
        // This is like BLOCK_OPEN but preserves LIST_MULTILINE context
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        $this->blockStack[] = self::BLOCK;
        return true;

      case 'BLOCK_OPEN':
        // Set expected indentation BEFORE pushing (current line indents at previous depth)
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        // Push BLOCK context to the stack
        $this->blockStack[] = self::BLOCK;
        return true;

      default:
        return false;
    }
  }

  /**
   * Handles list-related events (LIST_OPEN, LIST_CLOSE, etc.).
   * 
   * @param string $event_type The type of event
   * @param array<string, mixed> $event_data Additional event data
   * @param array<string, mixed> $line_info Line information to update
   * @return bool True if event was handled
   */
  private function handleListEvents(string $event_type, array $event_data, array &$line_info): bool {
    switch ($event_type) {
      case 'LIST_OPEN':
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        $this->blockStack[] = self::LIST_MULTILINE;
        return true;

      case 'LIST_CLOSE':
        // Pop all contexts up to and including LIST_MULTILINE (cascade pop)
        $this->popStackUntil(self::LIST_MULTILINE);
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        // If line ends with semicolon, also pop any remaining transient contexts
        if ($event_data['has_semicolon']) {
          while (in_array($this->getStackTop(), [self::CHAINED_BLOCK, self::TERNARY_OPERATOR])) {
            array_pop($this->blockStack);
          }
        }
        return true;

      case 'LIST_CLOSE_BLOCK_OPEN':
        // Close list (cascade pop), then open block on same line
        $this->popStackUntil(self::LIST_MULTILINE);
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        $this->blockStack[] = self::BLOCK;
        return true;

      case 'LIST_CLOSE_SEMICOLON':
        // Special case: ']); ' or similar - cascade pop to LIST_MULTILINE
        // Only if LIST_MULTILINE is actually on the stack
        if (in_array(self::LIST_MULTILINE, $this->blockStack, true)) {
          $this->popStackUntil(self::LIST_MULTILINE);
          $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
          // Also pop any remaining transient contexts (TERNARY_OPERATOR, CHAINED_BLOCK)
          while (in_array($this->getStackTop(), [self::CHAINED_BLOCK, self::TERNARY_OPERATOR])) {
            array_pop($this->blockStack);
          }
          return true;
        }
        // If no LIST_MULTILINE on stack, handle transient contexts
        // Calculate indentation BEFORE popping for closing lines
        // This handles cases like ternaries in parentheses: ($a ? $b : $c);
        if (in_array($this->getStackTop(), [self::CHAINED_BLOCK, self::TERNARY_OPERATOR])) {
          // Calculate indentation at current level (before popping)
          $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
          // Now pop transients: closing paren pops one, semicolon ends the statement
          while (in_array($this->getStackTop(), [self::CHAINED_BLOCK, self::TERNARY_OPERATOR])) {
            array_pop($this->blockStack);
          }
          return true;
        }
        // If no LIST_MULTILINE and no transients, treat as regular line
        return false;

      case 'GENERIC_LIST_OPEN':
        // Fallback for lines ending with list open tokens
        $this->pushIfNotTop(self::LIST_MULTILINE);
        return true;

      default:
        return false;
    }
  }

  /**
   * Handles switch/case-related events.
   * 
   * @param string $event_type The type of event
   * @param array<string, mixed> $event_data Additional event data
   * @param array<string, mixed> $line_info Line information to update
   * @return bool True if event was handled
   */
  private function handleSwitchCaseEvents(string $event_type, array $event_data, array &$line_info): bool {
    switch ($event_type) {
      case 'SWITCH_OPEN':
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        $this->blockStack[] = self::SWITCH_BLOCK;
        return true;

      case 'CASE_START':
        // Pop previous CASE_BLOCK if it exists
        $this->popIfTop(self::CASE_BLOCK);
        // Now calculate indentation at SWITCH_BLOCK level
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        // Push new CASE_BLOCK
        $this->blockStack[] = self::CASE_BLOCK;
        return true;

      case 'CASE_END':
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        $this->popIfTop(self::CASE_BLOCK);
        return true;

      case 'SWITCH_CASE_CLOSE':
        // Pop all contexts up to and including SWITCH_BLOCK (cascade pop)
        $this->popStackUntil(self::SWITCH_BLOCK);
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        return true;

      default: 
        return false;
    }
  }

  /**
   * Handles match expression events.
   * 
   * @param string $event_type The type of event
   * @param array<string, mixed> $event_data Additional event data
   * @param array<string, mixed> $line_info Line information to update
   * @param int $line_number Current line number
   * @param File $phpcs_file The file being processed
   * @return bool True if event was handled
   */
  private function handleMatchEvents(string $event_type, array $event_data, array &$line_info, int $line_number, File $phpcs_file): bool {
    switch ($event_type) {
      case 'MATCH_OPEN':
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        $this->blockStack[] = self::MATCH_EXPRESSION;
        return true;

      case 'MATCH_KEYWORD':
        // Special case: T_MATCH as last token, check if opening brace follows
        $tokens = $phpcs_file->getTokens();
        $found = false;
        foreach ($tokens as $tok) {
          if ($tok['line'] === $line_number && $tok['code'] === T_OPEN_CURLY_BRACKET) {
            $found = true;
            break;
          }
        }
        if ($found) {
          $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
          $this->blockStack[] = self::MATCH_EXPRESSION;
          return true;
        }
        return false;

      case 'MATCH_CLOSE':
        // Pop all contexts up to and including MATCH_EXPRESSION (cascade pop)
        $this->popStackUntil(self::MATCH_EXPRESSION);
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        return true;

      default:
        return false;

    }
  }

  /**
   * Handles method chaining events.
   * 
   * Method chaining adds +1 indentation level (2 spaces) to the current line.
   * We push CHAINED_BLOCK on stack to maintain context across chained lines.
   * 
   * @param string $event_type The type of event
   * @param array<string, mixed> $event_data Additional event data
   * @param array<string, mixed> $line_info Line information to update
   * @return bool True if event was handled
   */
  private function handleMethodChainEvents(string $event_type, array $event_data, array &$line_info): bool {
    if ($event_type !== 'METHOD_CHAIN') {
      return false;
    }

    // Push CHAINED_BLOCK context to maintain indentation across chain
    $this->pushIfNotTop(self::CHAINED_BLOCK);
    // Method chaining: calculate indentation based on stack depth
    $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;

    return true;
  }

  /**
   * Handles assignment-related events.
   * 
   * @param string $event_type The type of event
   * @param array<string, mixed> $event_data Additional event data
   * @param array<string, mixed> $line_info Line information to update
   * @return bool True if event was handled
   */
  private function handleAssignmentEvents(string $event_type, array $event_data, array &$line_info): bool {
    switch ($event_type) {
      case 'ASSIGNMENT_OPEN':
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        $this->blockStack[] = self::ASSIGNATION_MULTILINE;
        return true;

      case 'ASSIGNMENT_CLOSE':
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        // Only pop if top of stack is actually ASSIGNATION_MULTILINE
        $this->popIfTop(self::ASSIGNATION_MULTILINE);
        return true;

      default:
        return false;
    }
  }

  /**
   * Handles ternary operator events.
   * 
   * @param string $event_type The type of event
   * @param array<string, mixed> $event_data Additional event data
   * @param array<string, mixed> $line_info Line information to update
   * @return bool True if event was handled
   */
  private function handleTernaryEvents(string $event_type, array $event_data, array &$line_info): bool {
    switch ($event_type) {
      case 'TERNARY_THEN':
        // Push TERNARY_OPERATOR context
        $this->blockStack[] = self::TERNARY_OPERATOR;
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;
        // If line also ends with list open token, push LIST_MULTILINE
        if ($event_data['has_list_open']) {
          $this->blockStack[] = self::LIST_MULTILINE;
        }
        return true;

      case 'TERNARY_ELSE':
        // Pop all CHAINED_BLOCK contexts before handling ternary else
        while ($this->getStackTop() === self::CHAINED_BLOCK) {
          array_pop($this->blockStack);
        }

        // Calculate indentation at current level
        $line_info['expected_indent'] = count($this->blockStack) * self::SPACES;

        // If line ends with list open token, push LIST_MULTILINE
        if ($event_data['has_list_open'] ?? false) {
          $this->blockStack[] = self::LIST_MULTILINE;
          return true;
        }

        // If line ends with closing parenthesis, pop ONE TERNARY_OPERATOR (the one inside parens)
        if (($event_data['has_list_close'] ?? false) && $this->getStackTop() === self::TERNARY_OPERATOR) {
          array_pop($this->blockStack);
          return true;
        }

        // If line ends with semicolon, pop ALL TERNARY_OPERATOR contexts
        if (($event_data['has_semicolon'] ?? false) && $this->getStackTop() === self::TERNARY_OPERATOR) {
          while ($this->getStackTop() === self::TERNARY_OPERATOR) {
            array_pop($this->blockStack);
          }
          return true;
        }

        // If line ends with comma and LIST_MULTILINE is in stack, pop TERNARY_OPERATOR contexts
        if (
          ($event_data['has_comma'] ?? false)
          && in_array(self::LIST_MULTILINE, $this->blockStack, true)
        ) {
          while ($this->getStackTop() === self::TERNARY_OPERATOR) {
            array_pop($this->blockStack);
          }
          return true;
        }

        return false;

      default:  
        return false;

    }
  }

  /**
   * Detects what kind of event this line represents.
   * 
   * This is the foundation for the state machine refactoring.
   * 
   * @param array<int, string|int> $line_tokens Tokens on the line
   * @param int|string $first_token First significant token
   * @param int|string $last_token Last significant token
   * @return array{type: string, data: array<string, mixed>} Event type and associated data
   */
  private function detectEvent(array $line_tokens, $first_token, $last_token): array {
    // Block closure with immediate block opening (e.g., "} else {")
    if (
      $first_token === T_CLOSE_CURLY_BRACKET 
      && $last_token === T_OPEN_CURLY_BRACKET
      && $this->getStackTop() === self::BLOCK
    ) {
      return ['type' => 'BLOCK_CLOSE_BLOCK_OPEN', 'data' => []];
    }

    // Closure closure with comma (e.g., "},") - preserves LIST_MULTILINE
    // This must come BEFORE generic BLOCK_CLOSE to prevent stack corruption
    if (
      $first_token === T_CLOSE_CURLY_BRACKET
      && $last_token === T_COMMA
      && $this->getStackTop() === self::BLOCK
    ) {
      return ['type' => 'CLOSURE_CLOSE_COMMA', 'data' => []];
    }

    // Block closure
    if ($first_token === T_CLOSE_CURLY_BRACKET && $this->getStackTop() === self::BLOCK) {
      return ['type' => 'BLOCK_CLOSE', 'data' => []];
    }

    // Closure opening: function(...) { 
    // Detected by T_FUNCTION or T_CLOSURE as first token within a LIST_MULTILINE context
    // This should preserve the LIST_MULTILINE context on the stack
    if (
      in_array($first_token, [T_FUNCTION, T_CLOSURE])
      && $last_token === T_OPEN_CURLY_BRACKET
      && in_array(self::LIST_MULTILINE, $this->blockStack, true)
    ) {
      return ['type' => 'CLOSURE_OPEN', 'data' => []];
    }

    // Block opening (not after list closure, not switch, not match, not closure)
    if (
      $last_token === T_OPEN_CURLY_BRACKET
      && !in_array($first_token, self::LIST_MULTILINE_CLOSE_TOKENS)
      && $first_token !== T_SWITCH
      && !$this->isMatchExpression($line_tokens)
    ) {
      return ['type' => 'BLOCK_OPEN', 'data' => []];
    }

    // List opening
    if (
      !in_array($first_token, self::LIST_MULTILINE_CLOSE_TOKENS)
      && !in_array($first_token, [T_INLINE_THEN, T_INLINE_ELSE])
      && in_array($last_token, self::LIST_MULTILINE_OPEN_TOKENS)
    ) {
      return ['type' => 'LIST_OPEN', 'data' => []];
    }

    // List closure (simple)
    if (
      in_array($first_token, self::LIST_MULTILINE_CLOSE_TOKENS)
      && $last_token !== T_OPEN_CURLY_BRACKET
    ) {
      return ['type' => 'LIST_CLOSE', 'data' => ['has_semicolon' => $last_token === T_SEMICOLON]];
    }

    // List closure with block opening
    if (
      in_array($first_token, self::LIST_MULTILINE_CLOSE_TOKENS)
      && $last_token === T_OPEN_CURLY_BRACKET
    ) {
      return ['type' => 'LIST_CLOSE_BLOCK_OPEN', 'data' => []];
    }

    // List closure with semicolon (special case like ']);')
    // CRITICAL: Only match if line STARTS with closing token, not just contains one
    if (
      $last_token === T_SEMICOLON
      && count($line_tokens) >= 2
      && in_array($first_token, self::LIST_MULTILINE_CLOSE_TOKENS)
      && in_array($line_tokens[count($line_tokens) - 2], self::LIST_MULTILINE_CLOSE_TOKENS)
    ) {
      return ['type' => 'LIST_CLOSE_SEMICOLON', 'data' => []];
    }

    // Method chaining start
    if (in_array($first_token, [T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR])) {
      return ['type' => 'METHOD_CHAIN', 'data' => ['has_semicolon' => $last_token === T_SEMICOLON, 'has_comma' => $last_token === T_COMMA]];
    }

    // Match expression opening
    if ($last_token === T_OPEN_CURLY_BRACKET && $this->isMatchExpression($line_tokens)) {
      return ['type' => 'MATCH_OPEN', 'data' => []];
    }

    // Match expression with T_MATCH as last token (special case)
    if ($last_token === T_MATCH) {
      return ['type' => 'MATCH_KEYWORD', 'data' => []];
    }

    // Match expression closure
    if ($first_token === T_CLOSE_CURLY_BRACKET && $this->getStackTop() === self::MATCH_EXPRESSION) {
      return ['type' => 'MATCH_CLOSE', 'data' => []];
    }

    // Switch block opening
    if ($first_token === T_SWITCH) {
      return ['type' => 'SWITCH_OPEN', 'data' => []];
    }

    // Case/default block - detect when SWITCH_BLOCK is in stack (may have CASE_BLOCK on top)
    if (
      in_array($first_token, [T_CASE, T_DEFAULT]) 
      && in_array(self::SWITCH_BLOCK, $this->blockStack, true)
    ) {
      return ['type' => 'CASE_START', 'data' => []];
    }

    // Case block closure (return/break/throw)
    if (in_array($first_token, [T_RETURN, T_BREAK, T_THROW]) && $this->getStackTop() === self::CASE_BLOCK) {
      return ['type' => 'CASE_END', 'data' => []];
    }

    // Switch/case closure (closing brace)
    if ($first_token === T_CLOSE_CURLY_BRACKET && in_array($this->getStackTop(), [self::SWITCH_BLOCK, self::CASE_BLOCK])) {
      return ['type' => 'SWITCH_CASE_CLOSE', 'data' => []];
    }

    // Assignment multiline opening
    if ($last_token === T_EQUAL) {
      return ['type' => 'ASSIGNMENT_OPEN', 'data' => []];
    }

    // Assignment multiline closure
    if ($last_token === T_SEMICOLON && $this->getStackTop() === self::ASSIGNATION_MULTILINE) {
      return ['type' => 'ASSIGNMENT_CLOSE', 'data' => []];
    }

    // Ternary operator '?'
    if ($first_token === T_INLINE_THEN) {
      return ['type' => 'TERNARY_THEN', 'data' => ['has_list_open' => in_array($last_token, self::LIST_MULTILINE_OPEN_TOKENS)]];
    }

    // Ternary operator ':'
    if ($first_token === T_INLINE_ELSE) {
      return ['type' => 'TERNARY_ELSE', 'data' => [
        'has_semicolon' => $last_token === T_SEMICOLON,
        'has_comma' => $last_token === T_COMMA,
        'has_list_close' => in_array($last_token, self::LIST_MULTILINE_CLOSE_TOKENS),
        'has_list_open' => in_array($last_token, self::LIST_MULTILINE_OPEN_TOKENS)
      ]];
    }

    // Generic list opening (fallback for lines ending with list open tokens)
    if (in_array($last_token, self::LIST_MULTILINE_OPEN_TOKENS)) {
      return ['type' => 'GENERIC_LIST_OPEN', 'data' => []];
    }

    // Default: regular line
    return ['type' => 'REGULAR', 'data' => []];
  }

}
