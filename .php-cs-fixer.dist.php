<?php declare(strict_types=1);

/**
 * PHP-CS-Fixer configuration for NCAC coding standard.
 *
 * This configuration complements the NCAC PHPCS standard by providing
 * automatic fixes for complex transformations that are too risky for PHPCS.
 *
 * Usage:
 *   php-cs-fixer fix --config=vendor/ncac/phpcs-standard/.php-cs-fixer.dist.php
 *
 * Or copy this file to your project root as .php-cs-fixer.php and customize as needed.
 *
 * @author   NCAC
 * @category PHP_CodeSniffer
 * @package  NCAC
 * @license  https://github.com/NCAC/phpcs-standard/blob/main/LICENSE MIT License
 */

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new Finder())
  ->in(__DIR__)
  ->exclude([
    'vendor',
    'node_modules',
    'coverage',
    'coverage-html',
    'coverage-xml',
    'coverage-detailed',
    'coverage-report',
  ])
  ->name('*.php')
  ->ignoreDotFiles(true)
  ->ignoreVCS(true);

return (new Config())
  ->setParallelConfig(ParallelConfigFactory::detect()) // @TODO 4.0 no need to call this manually
  ->setRiskyAllowed(true)
  ->setIndent('  ')  // Use 2 spaces for indentation (NCAC standard)
  ->setRules([
    // ================================
    // ALTERNATE SYNTAX CONVERSION
    // ================================
    // Convert alternate control structure syntax to standard braces
    'no_alternative_syntax' => true,

    // ================================
    // SPACING AND FORMATTING
    // ================================
    // Basic formatting that works well with NCAC
    // PHP-CS-Fixer will use 2-space indentation (set via setIndent())
    'indentation_type' => true,                       // Ensure consistent spacing
    'line_ending' => true,
    'no_extra_blank_lines' => [
      'tokens' => [
        'extra',
        'throw',
        'use',
      ],
    ],
    'no_trailing_whitespace' => true,
    'no_trailing_whitespace_in_comment' => true,

    // ================================
    // BRACES AND CONTROL STRUCTURES
    // ================================
    // Ensure proper brace placement (complements NCAC rules)
    'braces' => [
      'allow_single_line_closure' => true,
      'position_after_functions_and_oop_constructs' => 'same',
      'position_after_control_structures' => 'same',
      'position_after_anonymous_constructs' => 'same',
    ],

    // Control structure spacing
    'control_structure_continuation_position' => [
      'position' => 'same_line',
    ],

    // ================================
    // IMPORTS AND NAMESPACES
    // ================================
    'no_unused_imports' => true,
    'ordered_imports' => [
      'sort_algorithm' => 'alpha',
    ],

    // ================================
    // PHP TAGS AND SYNTAX
    // ================================
    'full_opening_tag' => true,
    'no_closing_tag' => true,

    // ================================
    // TYPE HINTS AND MODERN PHP
    // ================================
    // Complements Slevomat type hint rules
    'declare_strict_types' => true,                    // Add declare(strict_types=1)
    'nullable_type_declaration_for_default_null_value' => true, // ?Type $param = null
    'phpdoc_to_param_type' => true,                   // Convert @param to native types
    'phpdoc_to_return_type' => true,                  // Convert @return to native types
    'phpdoc_to_property_type' => true,                // Convert @var to native property types

    // ================================
    // CLASS STRUCTURE AND VISIBILITY
    // ================================
    // Complements NCAC class structure rules
    'visibility_required' => true,                     // Require explicit visibility
    'class_attributes_separation' => [                 // Spacing between class elements
      'elements' => [
        'const' => 'one',
        'method' => 'one',
        'property' => 'one',
      ],
    ],
    'no_blank_lines_after_class_opening' => true,     // No blank line after class {
    'single_class_element_per_statement' => true,     // One property per line

    // ================================
    // CONSTANTS AND NAMING
    // ================================
    // Complements NCAC naming conventions
    'native_constant_invocation' => true,             // Use native constants (true vs TRUE)
    'native_function_invocation' => [                 // Use native functions without \
      'include' => ['@compiler_optimized'],
    ],

    // ================================
    // ARRAYS AND COLLECTIONS
    // ================================
    'array_syntax' => ['syntax' => 'short'],          // [] instead of array()
    'normalize_index_brace' => true,                  // $array['key'] not $array{"key"}
    'trim_array_spaces' => true,                      // No spaces in array indices
    'whitespace_after_comma_in_array' => true,       // Spaces after array commas

    // ================================
    // OPERATORS AND EXPRESSIONS
    // ================================
    'binary_operator_spaces' => [
      'default' => 'single_space',
    ],
    'unary_operator_spaces' => true,
    'concat_space' => ['spacing' => 'one'],           // Align with NCAC readability
    'ternary_operator_spaces' => true,                // Spaces around ? :
    'standardize_increment' => true,                  // ++$i instead of $i++
    'cast_spaces' => ['space' => 'single'],           // (int) $var

    // ================================
    // FUNCTION AND METHOD CALLS
    // ================================
    'no_spaces_after_function_name' => true,
    'no_spaces_inside_parenthesis' => true,
    'function_declaration' => [
      'closure_function_spacing' => 'one',
    ],
    'method_argument_space' => [
      'on_multiline' => 'ensure_fully_multiline',
    ],

    // ================================
    // COMMENTS AND DOCUMENTATION
    // ================================
    'single_line_comment_style' => ['comment_types' => ['hash']],
    'multiline_comment_opening_closing' => true,      // /* comment */ format
    'no_empty_comment' => true,                       // Remove empty comments

    // ================================
    // STRING AND QUOTES
    // ================================
    'single_quote' => ['strings_containing_single_quote_chars' => false],
    'escape_implicit_backslashes' => true,            // Escape backslashes in strings

    // ================================
    // SEMICOLONS AND SYNTAX
    // ================================
    'semicolon_after_instruction' => true,            // Ensure semicolons
    'no_singleline_whitespace_before_semicolons' => true,
    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],

    // ================================
    // RETURN STATEMENTS
    // ================================
    'simplified_null_return' => true,                 // return; instead of return null;
    'no_useless_return' => true,                      // Remove useless returns

  ])
  ->setFinder($finder);
