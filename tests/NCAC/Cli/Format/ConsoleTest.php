<?php declare(strict_types=1);

namespace NCAC\Tests\Cli\Format;

use NCAC\Cli\Format\Console;
use PHPUnit\Framework\TestCase;

/**
 * Output of the command: plain text unless the stream is a terminal.
 */
final class ConsoleTest extends TestCase {

  public function testStandardStreamsConsoleIsCreated(): void {
    // Nothing is written: the streams belong to the test runner.
    $this->assertInstanceOf(Console::class, Console::forStandardStreams());
  }

  public function testUndecoratedConsoleWritesPlainText(): void {
    $output = fopen('php://memory', 'w+');
    $error_output = fopen('php://memory', 'w+');
    $console = new Console($output, $error_output, false);
    $console->line('hello');
    $console->error('broken');
    rewind($output);
    rewind($error_output);
    $written = (string) stream_get_contents($output);
    $this->assertSame('hello' . \PHP_EOL, $written);
    $this->assertStringNotContainsString("\033[", $written);
    $this->assertStringContainsString('broken', (string) stream_get_contents($error_output));
  }

}
