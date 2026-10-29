<?php declare(strict_types=1);

namespace NCAC\Cli\Format;

/**
 * Where the project is and which configuration each pass uses.
 *
 * Resolution rules:
 * - the project root is the closest directory, from the working directory
 *   upwards, that contains a composer.json (the working directory otherwise);
 * - the PHPCS configuration is the first file of PHPCS_CONFIG_FILES found in
 *   the project root, in PHP_CodeSniffer's own lookup order; without one, the
 *   NCAC standard is used as is;
 * - the PHP-CS-Fixer configuration is the first file of FIXER_CONFIG_FILES
 *   found in the project root; without one, the configuration shipped with
 *   this package is used. PHP-CS-Fixer never runs without an explicit
 *   configuration (it would otherwise start its interactive set-up wizard).
 *
 * @package NCAC\Cli\Format
 */
final class ProjectContext {

  public const PHPCS_CONFIG_FILES = ['.phpcs.xml', 'phpcs.xml', '.phpcs.xml.dist', 'phpcs.xml.dist'];

  public const FIXER_CONFIG_FILES = ['.php-cs-fixer.php', '.php-cs-fixer.dist.php'];

  public const DEFAULT_STANDARD = 'NCAC';

  public const PACKAGE_FIXER_CONFIG = '.php-cs-fixer.dist.php';

  public readonly string $root;

  /**
   * Absolute path of the project's PHPCS configuration, null when the
   * default NCAC standard is used.
   */
  public readonly ?string $phpcsConfig;

  /**
   * Absolute path of the PHP-CS-Fixer configuration in use.
   */
  public readonly string $fixerConfig;

  public readonly bool $fixerConfigFromProject;

  public function __construct(string $root, ?string $phpcs_config, string $fixer_config, bool $fixer_config_from_project) {
    $this->root = $root;
    $this->phpcsConfig = $phpcs_config;
    $this->fixerConfig = $fixer_config;
    $this->fixerConfigFromProject = $fixer_config_from_project;
  }

  /**
   * @param string $working_directory Directory the command was started from.
   * @param string $package_directory Root directory of this package.
   */
  public static function resolve(string $working_directory, string $package_directory): self {
    $root = self::findProjectRoot($working_directory);
    $project_fixer_config = self::findFirst($root, self::FIXER_CONFIG_FILES);
    return new self(
      $root,
      self::findFirst($root, self::PHPCS_CONFIG_FILES),
      $project_fixer_config ?? $package_directory . \DIRECTORY_SEPARATOR . self::PACKAGE_FIXER_CONFIG,
      $project_fixer_config !== null
    );
  }

  private static function findProjectRoot(string $working_directory): string {
    $directory = $working_directory;
    while (!is_file($directory . \DIRECTORY_SEPARATOR . 'composer.json')) {
      $parent = \dirname($directory);
      if ($parent === $directory) {
        return $working_directory;
      }
      $directory = $parent;
    }
    return $directory;
  }

  /**
   * @param list<string> $candidates
   */
  private static function findFirst(string $directory, array $candidates): ?string {
    foreach ($candidates as $candidate) {
      $path = $directory . \DIRECTORY_SEPARATOR . $candidate;
      if (is_file($path)) {
        return $path;
      }
    }
    return null;
  }

  /**
   * Value of the --standard argument given to phpcs and phpcbf.
   */
  public function phpcsStandard(): string {
    return $this->phpcsConfig ?? self::DEFAULT_STANDARD;
  }

  /**
   * Formats an absolute path for display, relative to the project root when
   * it lies inside it.
   */
  public function displayPath(string $path): string {
    if ($path === $this->root) {
      return '.';
    }
    $prefix = $this->root . \DIRECTORY_SEPARATOR;
    if (str_starts_with($path, $prefix)) {
      return substr($path, \strlen($prefix));
    }
    return $path;
  }

}
