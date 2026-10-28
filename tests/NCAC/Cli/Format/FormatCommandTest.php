<?php declare(strict_types=1);

namespace NCAC\Tests\Cli\Format;

use NCAC\Cli\Format\Console;
use NCAC\Cli\Format\ExitCode;
use NCAC\Cli\Format\FormatCommand;
use NCAC\Cli\Format\ProcessRunner;
use NCAC\Cli\Format\Tool;
use PHPUnit\Framework\TestCase;

/**
 * Exit code contract of the command, with a scripted process runner.
 */
final class FormatCommandTest extends TestCase {

  private TemporaryDirectory $directory;

  /**
   * @var resource
   */
  private mixed $output;

  public function testFixSucceedsWhenValidationIsClean(): void {
    $runner = new ScriptedRunner([0, 1, 0]);
    $this->assertSame(ExitCode::SUCCESS, $this->format(['src'], $runner));
    $this->assertSame(['php-cs-fixer', 'phpcbf', 'phpcs'], $runner->tools());
  }

  public function testFixerFindingsDoNotDecideTheResult(): void {
    $this->assertSame(ExitCode::SUCCESS, $this->format(['src'], new ScriptedRunner([8 | 4, 1, 0])));
  }

  public function testFixReportsRemainingViolations(): void {
    $this->assertSame(ExitCode::VIOLATIONS, $this->format(['src'], new ScriptedRunner([0, 2, 2])));
  }

  public function testToolFailureStopsTheWorkflow(): void {
    $runner = new ScriptedRunner([16]);
    $this->assertSame(ExitCode::TOOL_FAILURE, $this->format(['src'], $runner));
    $this->assertSame(['php-cs-fixer'], $runner->tools());
  }

  public function testDryRunReportsPendingChanges(): void {
    $this->assertSame(ExitCode::VIOLATIONS, $this->format(['--dry-run', 'src'], new ScriptedRunner([8, 0])));
    $this->assertSame(ExitCode::VIOLATIONS, $this->format(['--dry-run', 'src'], new ScriptedRunner([0, 1])));
  }

  public function testDryRunIgnoresNonFixableViolations(): void {
    $this->assertSame(ExitCode::SUCCESS, $this->format(['--dry-run', 'src'], new ScriptedRunner([0, 2])));
  }

  public function testCheckOnlyRunsPhpcs(): void {
    $runner = new ScriptedRunner([2]);
    $this->assertSame(ExitCode::VIOLATIONS, $this->format(['--check', 'src'], $runner));
    $this->assertSame(['phpcs'], $runner->tools());
  }

  public function testToolsRunFromTheProjectRoot(): void {
    $runner = new ScriptedRunner([0]);
    $this->format(['--check', '.'], $runner, $this->directory->path . '/src');
    $this->assertSame([$this->directory->path], $runner->workingDirectories);
    $this->assertSame($this->directory->path . '/src', end($runner->commands[0]));
  }

  public function testUsageErrors(): void {
    $runner = new ScriptedRunner([]);
    $this->assertSame(ExitCode::USAGE_ERROR, $this->format(['--nope'], $runner));
    $this->assertSame(ExitCode::USAGE_ERROR, $this->format(['missing'], $runner));
    $this->assertSame([], $runner->commands);
  }

  public function testHelp(): void {
    $this->assertSame(ExitCode::SUCCESS, $this->format(['--help'], new ScriptedRunner([])));
    rewind($this->output);
    $this->assertStringContainsString('Usage:', (string) stream_get_contents($this->output));
  }

  public function testMissingToolIsAtoolFailure(): void {
    unlink($this->directory->path . '/vendor/bin/phpcs');
    $package_directory = $this->directory->mkdir('package');
    $console = new Console($this->output, $this->output, false);
    $command = new FormatCommand($package_directory, [], new ScriptedRunner([]), $console);
    $this->assertSame(ExitCode::TOOL_FAILURE, $command->run(['--check'], $this->directory->path));
  }

  protected function setUp(): void {
    $this->directory = new TemporaryDirectory();
    $this->directory->write('composer.json', '{}');
    $this->directory->mkdir('src');
    foreach (Tool::cases() as $tool) {
      $this->directory->write('vendor/bin/' . $tool->value);
    }
    $this->output = fopen('php://memory', 'w+');
  }

  protected function tearDown(): void {
    $this->directory->remove();
  }

  /**
   * @param list<string> $arguments
   */
  private function format(array $arguments, ScriptedRunner $runner, ?string $working_directory = null): int {
    $console = new Console($this->output, $this->output, false);
    $command = new FormatCommand($this->directory->path . '/package', [], $runner, $console);
    return $command->run($arguments, $working_directory ?? $this->directory->path);
  }

}

/**
 * Returns predefined exit codes and records the commands it receives.
 */
final class ScriptedRunner implements ProcessRunner {

  /**
   * @var list<list<string>>
   */
  public array $commands = [];

  /**
   * @var list<string>
   */
  public array $workingDirectories = [];

  /**
   * @var list<int>
   */
  private array $exitCodes;

  /**
   * @param list<int> $exit_codes
   */
  public function __construct(array $exit_codes) {
    $this->exitCodes = $exit_codes;
  }

  /**
   * @param list<string> $command
   */
  public function run(array $command, string $working_directory): int {
    $this->commands[] = $command;
    $this->workingDirectories[] = $working_directory;
    return array_shift($this->exitCodes) ?? 0;
  }

  /**
   * @return list<string>
   */
  public function tools(): array {
    return array_map(static fn (array $command): string => basename($command[1]), $this->commands);
  }

}
