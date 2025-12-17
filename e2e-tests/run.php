<?php declare(strict_types=1);

/**
 * Main entry point for E2E tests.
 *
 * @author   NCAC
 * @category PHP_CodeSniffer
 * @package  NCAC
 * @license  https://github.com/NCAC/phpcs-standard/blob/main/LICENSE MIT License
 */

// Autoload all test classes
spl_autoload_register(function ($class): void {
  $prefix = 'NCAC\\E2ETests\\';
  $base_dir = __DIR__ . '/';
  $len = \strlen($prefix);
  if (strncmp($prefix, $class, $len) !== 0) {
    return;
  }
  $relative_class = substr($class, $len);
  // Try different locations
  $locations = [
    $base_dir . str_replace('\\', '/', $relative_class) . '.php',
    $base_dir . 'tests/' . str_replace('\\', '/', $relative_class) . '.php',
  ];
  foreach ($locations as $file) {
    if (file_exists($file)) {
      require $file;
      return;
    }
  }
});

require_once __DIR__ . '/E2ETestRunner.php';
require_once __DIR__ . '/E2ETest.php';

// Run tests
$runner = new NCAC\E2ETests\E2ETestRunner(__DIR__ . '/..');
exit($runner->run());
