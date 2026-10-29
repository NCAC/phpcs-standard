<?php declare(strict_types=1);

namespace NCAC\Cli\Format;

/**
 * The tools orchestrated by ncac-format, with the meaning of their exit codes.
 *
 * @package NCAC\Cli\Format
 */
enum Tool: string {
  case Fixer = 'php-cs-fixer';

  case Phpcbf = 'phpcbf';

  case Phpcs = 'phpcs';

  /**
   * PHP-CS-Fixer: 4 = files with invalid syntax, 8 = files that need fixing.
   * Every other non-zero bit (1, 16, 32, 64) is an error of the tool itself.
   */
  private const FIXER_FINDING_BITS = 4 | 8;

  /**
   * PHP_CodeSniffer 4: 1 = fixable, 2 = non-fixable, 4 = failed to fix.
   * Every other non-zero bit (16, 64) is an error of the tool itself.
   */
  private const PHPCS_FINDING_BITS = 1 | 2 | 4;

  /**
   * Maps an exit code of this tool to an outcome.
   *
   * @param int $ignored_bits Finding bits that do not count as findings for
   *   the step at hand (they never hide a failure).
   */
  public function classify(int $exit_code, int $ignored_bits = 0): Outcome {
    $finding_bits = $this === self::Fixer ? self::FIXER_FINDING_BITS : self::PHPCS_FINDING_BITS;
    if ($exit_code < 0 || ($exit_code & ~$finding_bits) !== 0) {
      return Outcome::Failure;
    }
    if (($exit_code & ~$ignored_bits) === 0) {
      return Outcome::Clean;
    }
    return Outcome::Findings;
  }

  /**
   * Human readable name used in the console output.
   */
  public function label(): string {
    return match ($this) {
      self::Fixer => 'PHP-CS-Fixer',
      self::Phpcbf => 'PHPCBF',
      self::Phpcs => 'PHPCS',
    };
  }
}
