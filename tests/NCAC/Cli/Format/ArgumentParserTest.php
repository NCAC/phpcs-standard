<?php declare(strict_types=1);

namespace NCAC\Tests\Cli\Format;

use NCAC\Cli\Format\ArgumentParser;
use NCAC\Cli\Format\Mode;
use NCAC\Cli\Format\UsageException;
use PHPUnit\Framework\TestCase;

final class ArgumentParserTest extends TestCase {

  private TemporaryDirectory $directory;

  private ArgumentParser $parser;

  public function testDefaultsToFixModeOnTheWorkingDirectory(): void {
    $invocation = $this->parser->parse([], $this->directory->path);
    $this->assertSame(Mode::Fix, $invocation->mode);
    $this->assertSame([$this->directory->path], $invocation->paths);
    $this->assertFalse($invocation->help);
  }

  public function testModeOptions(): void {
    $this->assertSame(Mode::DryRun, $this->parser->parse(['--dry-run'], $this->directory->path)->mode);
    $this->assertSame(Mode::Check, $this->parser->parse(['--check'], $this->directory->path)->mode);
  }

  public function testRepeatingTheSameModeIsAllowed(): void {
    $this->assertSame(Mode::Check, $this->parser->parse(['--check', '--check'], $this->directory->path)->mode);
  }

  public function testConflictingModesAreRejected(): void {
    $this->expectException(UsageException::class);
    $this->expectExceptionMessage('mutually exclusive');
    $this->parser->parse(['--check', '--dry-run'], $this->directory->path);
  }

  public function testUnknownOptionsAreRejected(): void {
    $this->expectException(UsageException::class);
    $this->expectExceptionMessage('Unknown option: --fix');
    $this->parser->parse(['--fix'], $this->directory->path);
  }

  public function testRelativePathsAreResolvedAgainstTheWorkingDirectory(): void {
    $invocation = $this->parser->parse(['modules/custom'], $this->directory->path . '/web');
    $this->assertSame([$this->directory->path . '/web/modules/custom'], $invocation->paths);
  }

  public function testAbsolutePathsAreKept(): void {
    $invocation = $this->parser->parse([$this->directory->path . '/src'], '/');
    $this->assertSame([$this->directory->path . '/src'], $invocation->paths);
  }

  public function testSeveralPathsKeepTheirOrderWithoutDuplicates(): void {
    $invocation = $this->parser->parse(['web', 'src', './web/', 'src/../src'], $this->directory->path);
    $this->assertSame([$this->directory->path . '/web', $this->directory->path . '/src'], $invocation->paths);
  }

  public function testMissingPathsAreRejected(): void {
    $this->expectException(UsageException::class);
    $this->expectExceptionMessage('Path not found: nope');
    $this->parser->parse(['src', 'nope'], $this->directory->path);
  }

  public function testDoubleDashEndsOptions(): void {
    $invocation = $this->parser->parse(['--check', '--', '-dash'], $this->directory->path);
    $this->assertSame(Mode::Check, $invocation->mode);
    $this->assertSame([$this->directory->path . '/-dash'], $invocation->paths);
  }

  public function testHelpWinsOverEverythingElse(): void {
    $this->assertTrue($this->parser->parse(['--check', '--dry-run', 'nope', '--help'], $this->directory->path)->help);
    $this->assertTrue($this->parser->parse(['-h'], $this->directory->path)->help);
  }

  protected function setUp(): void {
    $this->directory = new TemporaryDirectory();
    $this->directory->mkdir('src');
    $this->directory->mkdir('web/modules/custom');
    $this->directory->mkdir('-dash');
    $this->parser = new ArgumentParser();
  }

  protected function tearDown(): void {
    $this->directory->remove();
  }

}
