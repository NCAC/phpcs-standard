<?php declare(strict_types=1);

namespace NCAC\Cli\Format;

/**
 * Runs an external command and returns its exit code.
 *
 * @package NCAC\Cli\Format
 */
interface ProcessRunner {

  /**
   * @param list<string> $command Executed without a shell.
   */
  public function run(array $command, string $working_directory): int;

}
