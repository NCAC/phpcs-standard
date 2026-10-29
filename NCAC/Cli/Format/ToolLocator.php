<?php declare(strict_types=1);

namespace NCAC\Cli\Format;

/**
 * Locates the Composer binaries of the orchestrated tools.
 *
 * Only Composer-installed binaries are considered: they are dependencies of
 * this package, so their versions are known. Binaries on the PATH are
 * deliberately ignored.
 *
 * @package NCAC\Cli\Format
 */
final class ToolLocator {

  /**
   * @var list<string>
   */
  private array $binDirectories;

  /**
   * @param list<string> $bin_directories Directories searched, in order.
   */
  public function __construct(array $bin_directories) {
    $this->binDirectories = $bin_directories;
  }

  /**
   * @throws ToolNotFoundException
   */
  public function locate(Tool $tool): string {
    foreach ($this->binDirectories as $bin_directory) {
      $path = $bin_directory . \DIRECTORY_SEPARATOR . $tool->value;
      if (is_file($path)) {
        return $path;
      }
    }
    throw new ToolNotFoundException(
      "{$tool->value} not found in: " . implode(', ', $this->binDirectories) . '. Run composer install.'
    );
  }

}
