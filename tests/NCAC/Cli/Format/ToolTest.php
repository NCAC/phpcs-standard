<?php declare(strict_types=1);

namespace NCAC\Tests\Cli\Format;

use NCAC\Cli\Format\Outcome;
use NCAC\Cli\Format\Tool;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ToolTest extends TestCase {

  /**
   * @return array<string, array{Tool, int, int, Outcome}>
   */
  public static function exitCodes(): array {
    return [
      'fixer ok' => [Tool::Fixer, 0, 0, Outcome::Clean],
      'fixer would change files' => [Tool::Fixer, 8, 0, Outcome::Findings],
      'fixer invalid syntax' => [Tool::Fixer, 4, 0, Outcome::Findings],
      'fixer general error' => [Tool::Fixer, 1, 0, Outcome::Failure],
      'fixer invalid config' => [Tool::Fixer, 16, 0, Outcome::Failure],
      'fixer exception' => [Tool::Fixer, 64, 0, Outcome::Failure],
      'fixer changes and invalid config' => [Tool::Fixer, 8 | 16, 0, Outcome::Failure],
      'phpcs ok' => [Tool::Phpcs, 0, 0, Outcome::Clean],
      'phpcs fixable' => [Tool::Phpcs, 1, 0, Outcome::Findings],
      'phpcs non fixable' => [Tool::Phpcs, 2, 0, Outcome::Findings],
      'phpcs both' => [Tool::Phpcs, 3, 0, Outcome::Findings],
      'phpcs process error' => [Tool::Phpcs, 16, 0, Outcome::Failure],
      'phpcs requirements' => [Tool::Phpcs, 64, 0, Outcome::Failure],
      'phpcbf failed to fix' => [Tool::Phpcbf, 4, 0, Outcome::Findings],
      'phpcs non fixable ignored' => [Tool::Phpcs, 2, 2, Outcome::Clean],
      'phpcs fixable not ignored' => [Tool::Phpcs, 3, 2, Outcome::Findings],
      'ignored bits never hide failures' => [Tool::Phpcs, 16, 16, Outcome::Failure],
      'negative exit code' => [Tool::Phpcs, -1, 0, Outcome::Failure],
    ];
  }

  #[DataProvider('exitCodes')]
  public function testClassify(Tool $tool, int $exit_code, int $ignored_bits, Outcome $expected): void {
    $this->assertSame($expected, $tool->classify($exit_code, $ignored_bits));
  }

}
