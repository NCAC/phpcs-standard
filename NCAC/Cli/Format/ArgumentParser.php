<?php declare(strict_types=1);

namespace NCAC\Cli\Format;

/**
 * Parses the ncac-format command line.
 *
 * Invariants of the returned Invocation:
 * - at most one mode option is given (--dry-run, --check);
 * - every path is resolved against the working directory of the caller,
 *   exists, and is returned as an absolute path;
 * - without any path, the working directory itself is the target.
 *
 * @package NCAC\Cli\Format
 */
final class ArgumentParser {

  private const MODE_OPTIONS = [
    '--dry-run' => Mode::DryRun,
    '--check' => Mode::Check,
  ];

  private const HELP_OPTIONS = ['--help', '-h'];

  /**
   * @param list<string> $arguments Command line arguments, without the script name.
   * @param string $working_directory Directory relative paths are resolved against.
   *
   * @throws UsageException
   */
  public function parse(array $arguments, string $working_directory): Invocation {
    $mode_options = [];
    $raw_paths = [];
    $help = false;
    $options_ended = false;
    foreach ($arguments as $argument) {
      if ($options_ended || !$this->isOption($argument)) {
        $raw_paths[] = $argument;
      } elseif ($argument === '--') {
        $options_ended = true;
      } elseif (\in_array($argument, self::HELP_OPTIONS, true)) {
        $help = true;
      } else {
        $mode_options[] = $this->modeOption($argument);
      }
    }
    if ($help) {
      return new Invocation(Mode::Fix, [], true);
    }
    return new Invocation(
      $this->resolveMode($mode_options),
      $this->resolvePaths($raw_paths, $working_directory),
      false
    );
  }

  private function isOption(string $argument): bool {
    return \strlen($argument) > 1 && $argument[0] === '-';
  }

  /**
   * @throws UsageException
   */
  private function modeOption(string $argument): string {
    if (!\array_key_exists($argument, self::MODE_OPTIONS)) {
      throw new UsageException("Unknown option: {$argument}");
    }
    return $argument;
  }

  /**
   * @param list<string> $mode_options
   *
   * @throws UsageException
   */
  private function resolveMode(array $mode_options): Mode {
    $distinct_options = array_values(array_unique($mode_options));
    if (\count($distinct_options) > 1) {
      throw new UsageException('Options ' . implode(' and ', $distinct_options) . ' are mutually exclusive.');
    }
    if ($distinct_options === []) {
      return Mode::Fix;
    }
    return self::MODE_OPTIONS[$distinct_options[0]];
  }

  /**
   * @param list<string> $raw_paths
   *
   * @return list<string>
   *
   * @throws UsageException
   */
  private function resolvePaths(array $raw_paths, string $working_directory): array {
    if ($raw_paths === []) {
      $raw_paths = ['.'];
    }
    $resolved_paths = [];
    foreach ($raw_paths as $raw_path) {
      $resolved_path = realpath($this->absolutePath($raw_path, $working_directory));
      if ($resolved_path === false) {
        throw new UsageException("Path not found: {$raw_path}");
      }
      if (!\in_array($resolved_path, $resolved_paths, true)) {
        $resolved_paths[] = $resolved_path;
      }
    }
    return $resolved_paths;
  }

  private function absolutePath(string $path, string $working_directory): string {
    $is_absolute = str_starts_with($path, '/')
    || str_starts_with($path, '\\')
    || preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1;
    if ($is_absolute) {
      return $path;
    }
    return rtrim($working_directory, '/\\') . \DIRECTORY_SEPARATOR . $path;
  }

}
