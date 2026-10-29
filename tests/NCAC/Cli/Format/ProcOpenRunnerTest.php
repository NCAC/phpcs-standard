<?php declare(strict_types=1);

namespace NCAC\Tests\Cli\Format;

use NCAC\Cli\Format\ProcOpenRunner;
use PHPUnit\Framework\TestCase;

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

  public function testMissingExecutableExitsWith127(): void {
    // proc_open() warns about the failed exec: keep the warning out of the report.
    set_error_handler(static fn (): bool => true);
    try {
      $this->assertSame(127, (new ProcOpenRunner())->run([$this->directory->path . '/missing'], $this->directory->path));
    } finally {
      restore_error_handler();
    }
  }

  protected function setUp(): void {
    $this->directory = new TemporaryDirectory();
  }

  protected function tearDown(): void {
    $this->directory->remove();
  }

}
