<?php declare(strict_types=1);

namespace NCAC\Cli\Format;

/**
 * Turns an invocation and a project context into the list of steps to run.
 *
 * - fix:     PHP-CS-Fixer fix, PHPCBF, PHPCS
 * - dry-run: PHP-CS-Fixer fix --dry-run --diff, PHPCS --report=diff
 *            (non-fixable violations do not count: a dry run reports
 *            changes, --check reports violations)
 * - check:   PHPCS
 *
 * PHP-CS-Fixer always runs with --path-mode=intersection, so the target paths
 * narrow the configured Finder instead of replacing it (its exclusions keep
 * applying), and with --no-interaction.
 *
 * @package NCAC\Cli\Format
 */
final class WorkflowPlanner {

  /**
   * Ignore patterns applied only when the default NCAC standard is used. A
   * project configuration owns its own exclusions.
   */
  public const DEFAULT_IGNORE = '*/vendor/*,*/node_modules/*';

  /**
   * PHP_CodeSniffer exit code bit: non-fixable violations found.
   */
  private const PHPCS_NON_FIXABLE = 2;

  private ToolLocator $locator;

  private string $phpBinary;

  public function __construct(ToolLocator $locator, string $php_binary) {
    $this->locator = $locator;
    $this->phpBinary = $php_binary;
  }

  /**
   * @return list<Step>
   *
   * @throws ToolNotFoundException
   */
  public function plan(Invocation $invocation, ProjectContext $context): array {
    return match ($invocation->mode) {
      Mode::Fix => [
        $this->fixerStep('Pass 1/2', $context, $invocation->paths, []),
        $this->phpcsStep('Pass 2/2', Tool::Phpcbf, $context, $invocation->paths, []),
        $this->phpcsStep('Validation', Tool::Phpcs, $context, $invocation->paths, []),
      ],
      Mode::DryRun => [
        $this->fixerStep('Pass 1/2 (preview)', $context, $invocation->paths, ['--dry-run', '--diff']),
        $this->phpcsStep('Pass 2/2 (preview)', Tool::Phpcs, $context, $invocation->paths, ['--report=diff'], self::PHPCS_NON_FIXABLE),
      ],
      Mode::Check => [
        $this->phpcsStep('Validation', Tool::Phpcs, $context, $invocation->paths, []),
      ],
    };
  }

  /**
   * @param list<string> $paths
   * @param list<string> $extra_arguments
   *
   * @throws ToolNotFoundException
   */
  private function fixerStep(string $title, ProjectContext $context, array $paths, array $extra_arguments): Step {
    $arguments = [
      'fix',
      '--config=' . $context->fixerConfig,
      '--path-mode=intersection',
      '--no-interaction',
      ...$extra_arguments,
      ...$paths,
    ];
    return $this->step($title, Tool::Fixer, $arguments);
  }

  /**
   * @param list<string> $paths
   * @param list<string> $extra_arguments
   *
   * @throws ToolNotFoundException
   */
  private function phpcsStep(
    string $title,
    Tool $tool,
    ProjectContext $context,
    array $paths,
    array $extra_arguments,
    int $ignored_bits = 0
  ): Step {
    $arguments = ['--standard=' . $context->phpcsStandard()];
    if ($context->phpcsConfig === null) {
      $arguments[] = '--ignore=' . self::DEFAULT_IGNORE;
    }
    return $this->step($title, $tool, [...$arguments, ...$extra_arguments, ...$paths], $ignored_bits);
  }

  /**
   * @param list<string> $arguments
   *
   * @throws ToolNotFoundException
   */
  private function step(string $title, Tool $tool, array $arguments, int $ignored_bits = 0): Step {
    $command = [$this->phpBinary, $this->locator->locate($tool), ...$arguments];
    return new Step($title, $tool, $command, $ignored_bits);
  }

}
