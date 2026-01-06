<?php

declare(strict_types=1);

namespace NCAC\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Test the ncac-format binary command
 */
final class NcacFormatCommandTest extends TestCase {

  private string $binPath;

  public function testBinaryExists(): void {
    $this->assertFileExists($this->binPath, 'ncac-format binary should exist');
  }

  public function testBinaryIsExecutable(): void {
    $this->assertTrue(is_executable($this->binPath), 'ncac-format binary should be executable');
  }

  public function testBinaryHasPhpShebang(): void {
    $content = file_get_contents($this->binPath);
    $this->assertStringStartsWith('#!/usr/bin/env php', $content, 'Binary should have PHP shebang');
  }

  public function testHelpOptionWorks(): void {
    $output = shell_exec("php {$this->binPath} --help 2>&1");
    $this->assertStringContainsString('NCAC Code Formatter', $output);
    $this->assertStringContainsString('Usage:', $output);
    $this->assertStringContainsString('--dry-run', $output);
    $this->assertStringContainsString('--help', $output);
  }

  public function testHelpOptionWithShortFlag(): void {
    $output = shell_exec("php {$this->binPath} -h 2>&1");
    $this->assertStringContainsString('NCAC Code Formatter', $output);
    $this->assertStringContainsString('Usage:', $output);
  }

  public function testBinaryExitsSuccessfullyWithHelp(): void {
    exec("php {$this->binPath} --help 2>&1", $output, $exit_code);
    $this->assertSame(0, $exit_code, 'Help command should exit with code 0');
  }

  protected function setUp(): void {
    $this->binPath = \dirname(__DIR__, 2) . '/bin/ncac-format';
  }

}
