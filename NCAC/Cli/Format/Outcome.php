<?php declare(strict_types=1);

namespace NCAC\Cli\Format;

/**
 * Interpretation of a tool's exit code.
 *
 * @package NCAC\Cli\Format
 */
enum Outcome {
  /**
   * Nothing to report.
   */
  case Clean;

  /**
   * The tool ran correctly and reported changes or violations.
   */
  case Findings;

  /**
   * The tool did not run correctly (configuration error, crash, ...).
   */
  case Failure;
}
