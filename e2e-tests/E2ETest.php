<?php

/**
 * Base class for E2E tests.
 *
 * @author   NCAC
 * @category PHP_CodeSniffer
 * @package  NCAC
 */

namespace NCAC\E2ETests;

abstract class E2ETest {

  protected E2ETestRunner $runner;

  public function __construct(E2ETestRunner $runner) {
    $this->runner = $runner;
  }

  /**
   * Get the test name.
   */
  abstract public function getName(): string;

  /**
   * Run the test.
   */
  abstract public function run(): void;

  /**
   * Print a step message.
   */
  protected function step(string $message): void {
    echo "  ➜ {$message}\n";
  }

  /**
   * Print a success message.
   */
  protected function success(string $message): void {
    echo "    ✓ {$message}\n";
  }

  /**
   * Print an info message.
   */
  protected function info(string $message): void {
    echo "    ℹ {$message}\n";
  }
}
