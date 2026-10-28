<?php declare(strict_types=1);

namespace NCAC\Cli\Format;

/**
 * A parsed command line.
 *
 * @package NCAC\Cli\Format
 */
final class Invocation {

  public readonly Mode $mode;

  /**
   * Absolute, existing, de-duplicated target paths, in the order given.
   *
   * @var list<string>
   */
  public readonly array $paths;

  public readonly bool $help;

  /**
   * @param list<string> $paths
   */
  public function __construct(Mode $mode, array $paths, bool $help) {
    $this->mode = $mode;
    $this->paths = $paths;
    $this->help = $help;
  }

}
