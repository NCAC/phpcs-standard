<?php declare(strict_types=1);

namespace NCAC\Cli\Format;

/**
 * Minimal console output. Colors are used only when the output is a terminal
 * and the NO_COLOR environment variable is not set.
 *
 * @package NCAC\Cli\Format
 */
final class Console {

  private const RESET = "\033[0m";

  private const BOLD = "\033[1m";

  private const BLUE = "\033[34m";

  private const GREEN = "\033[32m";

  private const YELLOW = "\033[33m";

  private const RED = "\033[31m";

  /**
   * @var resource
   */
  private mixed $output;

  /**
   * @var resource
   */
  private mixed $errorOutput;

  private bool $decorated;

  /**
   * @param resource $output
   * @param resource $error_output
   */
  public function __construct(mixed $output, mixed $error_output, bool $decorated) {
    $this->output = $output;
    $this->errorOutput = $error_output;
    $this->decorated = $decorated;
  }

  public static function forStandardStreams(): self {
    $decorated = getenv('NO_COLOR') === false && stream_isatty(\STDOUT);
    return new self(\STDOUT, \STDERR, $decorated);
  }

  public function line(string $text = ''): void {
    fwrite($this->output, $text . \PHP_EOL);
  }

  public function title(string $text): void {
    $this->line($this->style(self::BOLD, $text));
  }

  public function step(string $text): void {
    $this->line();
    $this->line($this->style(self::BLUE . self::BOLD, '➜ ' . $text));
  }

  public function success(string $text): void {
    $this->line($this->style(self::GREEN . self::BOLD, $text));
  }

  public function warning(string $text): void {
    $this->line($this->style(self::YELLOW . self::BOLD, $text));
  }

  public function error(string $text): void {
    fwrite($this->errorOutput, $this->style(self::RED . self::BOLD, $text) . \PHP_EOL);
  }

  private function style(string $codes, string $text): string {
    return $this->decorated ? $codes . $text . self::RESET : $text;
  }

}
