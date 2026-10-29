<?php declare(strict_types=1);

namespace NCAC\Cli\Format;

/**
 * Execution mode of the ncac-format command.
 *
 * @package NCAC\Cli\Format
 */
enum Mode: string {
  /**
   * Pass 1 (PHP-CS-Fixer) and pass 2 (PHPCBF) rewrite files, then PHPCS validates.
   */
  case Fix = 'fix';

  /**
   * Both passes print the diff they would apply. No file is written.
   */
  case DryRun = 'dry-run';

  /**
   * PHPCS validation only. No file is written.
   */
  case Check = 'check';
}
