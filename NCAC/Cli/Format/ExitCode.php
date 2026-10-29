<?php declare(strict_types=1);

namespace NCAC\Cli\Format;

/**
 * Exit codes of the ncac-format command.
 *
 * The values are part of the public contract of the command: CI jobs and git
 * hooks rely on them.
 *
 * @package NCAC\Cli\Format
 */
final class ExitCode {

  /**
   * Fix / check: the targets comply with the standard.
   * Dry run: nothing would be changed and nothing is reported.
   */
  public const SUCCESS = 0;

  /**
   * Fix / check: violations remain that need a manual fix.
   * Dry run: at least one pass would change a file (or PHP-CS-Fixer found
   * a file with invalid syntax).
   */
  public const VIOLATIONS = 1;

  /**
   * Invalid invocation: unknown option, conflicting modes, missing path.
   */
  public const USAGE_ERROR = 2;

  /**
   * A tool is missing or failed (configuration error, crash). The run stops
   * at the first failing step.
   */
  public const TOOL_FAILURE = 3;

}
