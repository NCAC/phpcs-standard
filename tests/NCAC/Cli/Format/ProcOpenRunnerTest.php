<?php declare(strict_types=1);

namespace NCAC\Tests\Cli\Format;

use NCAC\Cli\Format\ProcOpenRunner;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * The real process runner, driven with the PHP binary running this test.
 */
final class ProcOpenRunnerTest extends TestCase {

  private TemporaryDirectory $directory;

  public function testReturnsTheExitCodeOfTheCommand(): void {
    $runner = new ProcOpenRunner();
    $this->assertSame(0, $runner->run([\PHP_BINARY, '-r', 'exit(0);'], $this->directory->path));
    $this->assertSame(3, $runner->run([\PHP_BINARY, '-r', 'exit(3);'], $this->directory->path));
  }

  public function testRunsInTheWorkingDirectory(): void {
    $expected = (string) realpath($this->directory->path);
    $code = 'exit(getcwd() === $argv[1] ? 0 : 1);';
    $this->assertSame(0, (new ProcOpenRunner())->run([\PHP_BINARY, '-r', $code, $expected], $expected));
  }

  public function testMissingExecutableFailsToRun(): void {
    // Depending on the PHP version, proc_open() either starts the process and the
    // exec fails (exit code 127), or refuses to start it (the runner then throws).
    // It also warns in both cases: keep the warning out of the report.
    set_error_handler(static fn (): bool => true);
    try {
      $outcome = (new ProcOpenRunner())->run([$this->directory->path . '/missing'], $this->directory->path);
    } catch (RuntimeException $exception) {
      $outcome = $exception;
    } finally {
      restore_error_handler();
    }
    $this->assertTrue($outcome === 127 || $outcome instanceof RuntimeException);
  }

  protected function setUp(): void {
    $this->directory = new TemporaryDirectory();
  }

  protected function tearDown(): void {
    $this->directory->remove();
  }

}
