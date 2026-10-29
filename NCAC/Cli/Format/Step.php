<?php declare(strict_types=1);

namespace NCAC\Cli\Format;

/**
 * One tool execution of the workflow.
 *
 * @package NCAC\Cli\Format
 */
final class Step {

  public readonly string $title;

  public readonly Tool $tool;

  /**
   * Full command, executed without a shell: [php binary, tool script, ...arguments].
   *
   * @var list<string>
   */
  public readonly array $command;

  /**
   * Exit code bits that do not count as findings for this step.
   *
   * @see Tool::classify()
   */
  public readonly int $ignoredBits;

  /**
   * @param list<string> $command
   */
  public function __construct(string $title, Tool $tool, array $command, int $ignored_bits = 0) {
    $this->title = $title;
    $this->tool = $tool;
    $this->command = $command;
    $this->ignoredBits = $ignored_bits;
  }

  public function classify(int $exit_code): Outcome {
    return $this->tool->classify($exit_code, $this->ignoredBits);
  }

}
