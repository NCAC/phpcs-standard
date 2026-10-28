<?php declare(strict_types=1);

namespace NCAC\Cli\Format;

use RuntimeException;

/**
 * ProcessRunner based on proc_open, sharing the standard streams of the
 * current process (the tools write directly to the terminal).
 *
 * @package NCAC\Cli\Format
 */
final class ProcOpenRunner implements ProcessRunner {

  /**
   * @param list<string> $command
   */
  public function run(array $command, string $working_directory): int {
    $pipes = [];
    $process = proc_open($command, [0 => \STDIN, 1 => \STDOUT, 2 => \STDERR], $pipes, $working_directory);
    if ($process === false) {
      throw new RuntimeException('Unable to start: ' . implode(' ', $command));
    }
    return proc_close($process);
  }

}
