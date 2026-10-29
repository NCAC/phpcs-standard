<?php declare(strict_types=1);

namespace NCAC\Cli\Format;

use RuntimeException;

/**
 * The ncac-format command: formats PHP code to the NCAC standard in two passes
 * (PHP-CS-Fixer, then PHPCBF) and validates the result with PHPCS.
 *
 * @package NCAC\Cli\Format
 */
final class FormatCommand {

  public const HELP = <<<'TXT'
ncac-format - format PHP code to the NCAC standard in two passes

Usage:
  ncac-format [--dry-run | --check] [--] [<path>...]

Passes:
  1. PHP-CS-Fixer   structural rewrites (alternative syntax, imports, strict_types, ...)
  2. PHPCBF         NCAC rules (2-space indentation, naming, class spacing, ...)
  Then PHPCS validates the result and lists what needs a manual fix.

Modes:
  (default)    fix files in place, then validate
  --dry-run    print the diff of both passes, write nothing
  --check      validate only (PHPCS), write nothing; meant for CI and git hooks
  -h, --help   show this help

Paths:
  Relative to the current directory. Default: the current directory.

Configuration, looked up in the project root (closest directory with a composer.json):
  PHPCS         .phpcs.xml, phpcs.xml, .phpcs.xml.dist, phpcs.xml.dist
                otherwise the NCAC standard
  PHP-CS-Fixer  .php-cs-fixer.php, .php-cs-fixer.dist.php
                otherwise the configuration shipped with ncac/phpcs-standard

Exit codes:
  0  compliant (dry-run: nothing to change)
  1  violations remain (dry-run: at least one pass would change a file)
  2  invalid usage
  3  a tool is missing or failed
TXT;

  private string $packageDirectory;

  /**
   * @var list<string>
   */
  private array $binDirectories;

  private ProcessRunner $runner;

  private Console $console;

  /**
   * @param string $package_directory Root directory of this package.
   * @param list<string> $bin_directories Extra Composer bin directories, searched first.
   */
  public function __construct(string $package_directory, array $bin_directories, ProcessRunner $runner, Console $console) {
    $this->packageDirectory = $package_directory;
    $this->binDirectories = $bin_directories;
    $this->runner = $runner;
    $this->console = $console;
  }

  /**
   * @param list<string> $arguments Command line arguments, without the script name.
   */
  public function run(array $arguments, string $working_directory): int {
    try {
      $invocation = (new ArgumentParser())->parse($arguments, $working_directory);
    } catch (UsageException $exception) {
      $this->console->error($exception->getMessage());
      $this->console->error('Run ncac-format --help for usage.');
      return ExitCode::USAGE_ERROR;
    }
    if ($invocation->help) {
      $this->console->line(self::HELP);
      return ExitCode::SUCCESS;
    }
    $context = ProjectContext::resolve($working_directory, $this->packageDirectory);
    try {
      $steps = $this->planner($context)->plan($invocation, $context);
    } catch (ToolNotFoundException $exception) {
      $this->console->error($exception->getMessage());
      return ExitCode::TOOL_FAILURE;
    }
    $this->printHeader($invocation, $context);
    return $this->execute($steps, $invocation->mode, $context->root);
  }

  private function planner(ProjectContext $context): WorkflowPlanner {
    $bin_directories = [
      ...$this->binDirectories,
      $context->root . \DIRECTORY_SEPARATOR . 'vendor' . \DIRECTORY_SEPARATOR . 'bin',
      $this->packageDirectory . \DIRECTORY_SEPARATOR . 'vendor' . \DIRECTORY_SEPARATOR . 'bin',
    ];
    return new WorkflowPlanner(new ToolLocator(array_values(array_unique($bin_directories))), \PHP_BINARY);
  }

  private function printHeader(Invocation $invocation, ProjectContext $context): void {
    $phpcs_config = $context->phpcsConfig === null
      ? ProjectContext::DEFAULT_STANDARD . ' (default standard)'
      : $context->displayPath($context->phpcsConfig) . ' (project)';
    $fixer_config = $context->fixerConfigFromProject
      ? $context->displayPath($context->fixerConfig) . ' (project)'
      : $context->displayPath($context->fixerConfig) . ' (package default)';
    $paths = array_map([$context, 'displayPath'], $invocation->paths);
    $this->console->title('NCAC format - ' . $invocation->mode->value);
    $this->console->line('  Project root : ' . $context->root);
    $this->console->line('  PHPCS config : ' . $phpcs_config);
    $this->console->line('  Fixer config : ' . $fixer_config);
    $this->console->line('  Paths        : ' . implode(', ', $paths));
  }

  /**
   * @param list<Step> $steps
   */
  private function execute(array $steps, Mode $mode, string $working_directory): int {
    $outcomes = [];
    foreach ($steps as $step) {
      $this->console->step($step->title . ' - ' . $step->tool->label());
      try {
        $exit_code = $this->runner->run($step->command, $working_directory);
      } catch (RuntimeException $exception) {
        $this->console->line();
        $this->console->error($exception->getMessage() . '. Remaining steps skipped.');
        return ExitCode::TOOL_FAILURE;
      }
      $outcome = $step->classify($exit_code);
      if ($outcome === Outcome::Failure) {
        $this->console->line();
        $this->console->error("{$step->tool->label()} failed (exit code {$exit_code}). Remaining steps skipped.");
        return ExitCode::TOOL_FAILURE;
      }
      $outcomes[] = $outcome;
    }
    return $this->conclude($mode, $outcomes);
  }

  /**
   * @param list<Outcome> $outcomes
   */
  private function conclude(Mode $mode, array $outcomes): int {
    $this->console->line();
    if ($mode === Mode::DryRun) {
      $has_findings = \in_array(Outcome::Findings, $outcomes, true);
      $this->console->line($has_findings ? 'Changes previewed above. Nothing was written.' : 'Nothing to change.');
      return $has_findings ? ExitCode::VIOLATIONS : ExitCode::SUCCESS;
    }
    if (end($outcomes) === Outcome::Clean) {
      $this->console->success('Compliant: no violation left.');
      return ExitCode::SUCCESS;
    }
    $this->console->warning('Violations remain and need a manual fix (see the PHPCS report above).');
    return ExitCode::VIOLATIONS;
  }

}
