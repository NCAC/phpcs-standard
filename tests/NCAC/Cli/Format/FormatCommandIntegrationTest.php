<?php declare(strict_types=1);

namespace NCAC\Tests\Cli\Format;

use PHPUnit\Framework\TestCase;

/**
 * Runs the real bin/ncac-format, with the real tools, on throw-away projects.
 */
final class FormatCommandIntegrationTest extends TestCase {

  private const HOOK = <<<'PHP'
<?php declare(strict_types=1);

function mymodule_preprocess_node__homepage(): void {
  $variables = ["x" => 1];
}

PHP;

  private const DRUPAL_RULESET = <<<'XML'
<?xml version="1.0"?>
<ruleset name="Project">
  <rule ref="NCAC"/>
  <rule ref="NCAC.NamingConventions.FunctionName">
    <properties>
      <property name="allowDoubleUnderscore" value="true"/>
    </properties>
  </rule>
</ruleset>

XML;

  private TemporaryDirectory $project;

  public function testProjectPhpcsConfigurationIsHonoured(): void {
    $this->project->write('phpcs.xml', self::DRUPAL_RULESET);
    [$exit_code, $output] = $this->ncacFormat(['src'], $this->project->path);
    $this->assertSame(0, $exit_code, $output);
    $fixed = (string) file_get_contents($this->project->path . '/src/hooks.php');
    $this->assertStringContainsString('function mymodule_preprocess_node__homepage()', $fixed);
    $this->assertStringContainsString("['x' => 1]", $fixed, 'pass 1 (PHP-CS-Fixer) must have run');
    $this->assertStringContainsString('phpcs.xml (project)', $output);
  }

  public function testDefaultStandardAppliesWithoutProjectConfiguration(): void {
    [$exit_code, $output] = $this->ncacFormat(['src'], $this->project->path);
    $this->assertSame(0, $exit_code, $output);
    $fixed = (string) file_get_contents($this->project->path . '/src/hooks.php');
    $this->assertStringContainsString('function mymodule_preprocess_node_homepage()', $fixed);
  }

  public function testPathsAreRelativeToTheCurrentDirectory(): void {
    [$exit_code, $output] = $this->ncacFormat(['--check', 'hooks.php'], $this->project->path . '/src');
    $this->assertSame(1, $exit_code, $output);
    $this->assertStringContainsString('src/hooks.php', $output);
  }

  public function testNoInteractivePromptAndNoFileCreatedInTheProject(): void {
    [$exit_code, $output] = $this->ncacFormat(['--dry-run', 'src'], $this->project->path);
    $this->assertSame(1, $exit_code, $output);
    $this->assertSame(self::HOOK, file_get_contents($this->project->path . '/src/hooks.php'), 'dry run must not write');
    $this->assertFileDoesNotExist($this->project->path . '/.php-cs-fixer.dist.php');
    $this->assertFileDoesNotExist($this->project->path . '/.gitignore');
  }

  public function testCheckSucceedsAfterFix(): void {
    $this->ncacFormat(['src'], $this->project->path);
    [$exit_code, $output] = $this->ncacFormat(['--check', 'src'], $this->project->path);
    $this->assertSame(0, $exit_code, $output);
  }

  protected function setUp(): void {
    $this->project = new TemporaryDirectory();
    $this->project->write('composer.json', '{"require": {"php": "^8.2"}}');
    $this->project->write('src/hooks.php', self::HOOK);
  }

  protected function tearDown(): void {
    $this->project->remove();
  }

  /**
   * @param list<string> $arguments
   *
   * @return array{int, string}
   */
  private function ncacFormat(array $arguments, string $working_directory): array {
    $command = [\PHP_BINARY, \dirname(__DIR__, 4) . '/bin/ncac-format', ...$arguments];
    $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['redirect', 1]];
    $process = proc_open($command, $descriptors, $pipes, $working_directory, ['NO_COLOR' => '1'] + getenv());
    $this->assertIsResource($process);
    fclose($pipes[0]);
    $output = (string) stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    return [proc_close($process), $output];
  }

}
