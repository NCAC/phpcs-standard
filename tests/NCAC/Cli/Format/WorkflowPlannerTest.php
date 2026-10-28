<?php declare(strict_types=1);

namespace NCAC\Tests\Cli\Format;

use NCAC\Cli\Format\Invocation;
use NCAC\Cli\Format\Mode;
use NCAC\Cli\Format\Outcome;
use NCAC\Cli\Format\ProjectContext;
use NCAC\Cli\Format\Step;
use NCAC\Cli\Format\Tool;
use NCAC\Cli\Format\ToolLocator;
use NCAC\Cli\Format\ToolNotFoundException;
use NCAC\Cli\Format\WorkflowPlanner;
use PHPUnit\Framework\TestCase;

final class WorkflowPlannerTest extends TestCase {

  private TemporaryDirectory $directory;

  private WorkflowPlanner $planner;

  private string $binDirectory;

  public function testFixModeRunsBothPassesThenValidates(): void {
    $steps = $this->planner->plan($this->invocation(Mode::Fix), $this->projectContext());
    $this->assertSame([Tool::Fixer, Tool::Phpcbf, Tool::Phpcs], $this->tools($steps));
    $this->assertSame(
      [
        '/usr/bin/php',
        $this->binDirectory . '/php-cs-fixer',
        'fix',
        '--config=/project/.php-cs-fixer.php',
        '--path-mode=intersection',
        '--no-interaction',
        '/project/src',
        '/project/web',
      ],
      $steps[0]->command
    );
    $this->assertSame(
      ['/usr/bin/php', $this->binDirectory . '/phpcbf', '--standard=/project/phpcs.xml', '/project/src', '/project/web'],
      $steps[1]->command
    );
    $this->assertSame(
      ['/usr/bin/php', $this->binDirectory . '/phpcs', '--standard=/project/phpcs.xml', '/project/src', '/project/web'],
      $steps[2]->command
    );
  }

  public function testDryRunPreviewsBothPasses(): void {
    $steps = $this->planner->plan($this->invocation(Mode::DryRun), $this->projectContext());
    $this->assertSame([Tool::Fixer, Tool::Phpcs], $this->tools($steps));
    $this->assertContains('--dry-run', $steps[0]->command);
    $this->assertContains('--diff', $steps[0]->command);
    $this->assertContains('--report=diff', $steps[1]->command);
  }

  public function testDryRunDoesNotCountNonFixableViolations(): void {
    $steps = $this->planner->plan($this->invocation(Mode::DryRun), $this->projectContext());
    $this->assertSame(Outcome::Clean, $steps[1]->classify(2));
    $this->assertSame(Outcome::Findings, $steps[1]->classify(1));
  }

  public function testCheckModeOnlyValidates(): void {
    $steps = $this->planner->plan($this->invocation(Mode::Check), $this->projectContext());
    $this->assertSame([Tool::Phpcs], $this->tools($steps));
    $this->assertSame(Outcome::Findings, $steps[0]->classify(2));
  }

  public function testDefaultStandardIgnoresDependencies(): void {
    $context = new ProjectContext('/project', null, '/package/.php-cs-fixer.dist.php', false);
    $steps = $this->planner->plan($this->invocation(Mode::Fix), $context);
    $this->assertSame(
      ['--standard=NCAC', '--ignore=*/vendor/*,*/node_modules/*', '/project/src', '/project/web'],
      \array_slice($steps[2]->command, 2)
    );
    $this->assertContains('--config=/package/.php-cs-fixer.dist.php', $steps[0]->command);
  }

  public function testProjectConfigurationOwnsItsExclusions(): void {
    $steps = $this->planner->plan($this->invocation(Mode::Check), $this->projectContext());
    foreach ($steps[0]->command as $argument) {
      $this->assertStringStartsNotWith('--ignore', $argument);
    }
  }

  public function testMissingToolIsReported(): void {
    unlink($this->binDirectory . '/phpcbf');
    $this->expectException(ToolNotFoundException::class);
    $this->expectExceptionMessage('phpcbf not found');
    $this->planner->plan($this->invocation(Mode::Fix), $this->projectContext());
  }

  protected function setUp(): void {
    $this->directory = new TemporaryDirectory();
    $this->binDirectory = $this->directory->mkdir('bin');
    foreach (Tool::cases() as $tool) {
      $this->directory->write('bin/' . $tool->value);
    }
    $this->planner = new WorkflowPlanner(new ToolLocator([$this->binDirectory]), '/usr/bin/php');
  }

  protected function tearDown(): void {
    $this->directory->remove();
  }

  private function invocation(Mode $mode): Invocation {
    return new Invocation($mode, ['/project/src', '/project/web'], false);
  }

  private function projectContext(): ProjectContext {
    return new ProjectContext('/project', '/project/phpcs.xml', '/project/.php-cs-fixer.php', true);
  }

  /**
   * @param list<Step> $steps
   *
   * @return list<Tool>
   */
  private function tools(array $steps): array {
    return array_map(static fn (Step $step): Tool => $step->tool, $steps);
  }

}
