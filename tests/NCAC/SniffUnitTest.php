<?php
/**
 * Abstract base class for all sniff unit tests.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings that are not found, or
 * warnings and errors that are not expected, are considered test failures.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace NCAC\Tests;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Util\Common;
use PHP_CodeSniffer\Util\Tokens;
use PHPUnit\Framework\TestCase;

abstract class SniffUnitTest extends TestCase {

  /**
   * Path to the standard's main directory.
   *
   */
  public ?string $standardsDir = null;

  /**
   * Path to the standard's test directory.
   *
   */
  public ?string $testsDir = null;

  /**
   * Enable or disable the backup and restoration of the $GLOBALS array.
   * Overwrite this attribute in a child class of TestCase.
   * Setting this attribute in setUp() has no effect.
   *
   */
  protected bool $backupGlobals = false;

  /**
   * Path to the root folder of the standard.
   *
   */
  private ?string $rootDir = null;

  /**
   * PHPUnit setUp. Initializes paths and required constants.
   */
  public function setUp(): void {
    $this->rootDir  = __DIR__ . '/../../';
    $this->testsDir = __DIR__ . '/';
    // Required to pull in all the defines from the tokens file.
    $tokens = new Tokens();
    if (!defined('PHP_CODESNIFFER_VERBOSITY')) {
      define('PHP_CODESNIFFER_VERBOSITY', 0);
    }
    if (!defined('PHP_CODESNIFFER_CBF')) {
      define('PHP_CODESNIFFER_CBF', 0);
    }
  }

  /**
   * Generates a list of test failures for a given sniffed file.
   *
   * @param LocalFile $file The file being tested.
   * @return string[]
   * @throws RuntimeException
   */
  public function generateFailureMessages(LocalFile $file): array {
    $test_file = $file->getFilename();
    $found_errors      = $file->getErrors();
    $found_warnings    = $file->getWarnings();
    $expected_errors   = $this->getErrorList(basename($test_file));
    $expected_warnings = $this->getWarningList(basename($test_file));
    $all_problems     = [];
    $failure_messages = [];
    $GLOBALS['PHP_CODESNIFFER_SNIFF_CODES']   = [];
    $GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES'] = [];
    foreach ($found_errors as $line => $line_errors) {
      foreach ($line_errors as $column => $errors) {
        if (!isset($all_problems[$line])) {
          $all_problems[$line] = [
            'expected_errors'   => 0,
            'expected_warnings' => 0,
            'found_errors'      => [],
            'found_warnings'    => [],
          ];
        }
        $found_errors_temp = [];
        foreach ($all_problems[$line]['found_errors'] as $found_error) {
          $found_errors_temp[] = $found_error;
        }
        $error_temp = [];
        foreach ($errors as $found_error) {
          $error_temp[] = $found_error['message'] . ' (' . $found_error['source'] . ')';
          $source = $found_error['source'];
          if (!in_array($source, $GLOBALS['PHP_CODESNIFFER_SNIFF_CODES'])) {
            $GLOBALS['PHP_CODESNIFFER_SNIFF_CODES'][] = $source;
          }
          if ($found_error['fixable'] === true && !in_array($source, $GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES'])) {
            $GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES'][] = $source;
          }
        }
        $all_problems[$line]['found_errors'] = array_merge($found_errors_temp, $error_temp);
      }
      if (isset($expected_errors[$line])) {
        $all_problems[$line]['expected_errors'] = $expected_errors[$line];
      } else {
        $all_problems[$line]['expected_errors'] = 0;
      }
      unset($expected_errors[$line]);
    }
    foreach ($expected_errors as $line => $num_errors) {
      if (!isset($all_problems[$line])) {
        $all_problems[$line] = [
          'expected_errors'   => 0,
          'expected_warnings' => 0,
          'found_errors'      => [],
          'found_warnings'    => [],
        ];
      }
      $all_problems[$line]['expected_errors'] = $num_errors;
    }
    foreach ($found_warnings as $line => $line_warnings) {
      foreach ($line_warnings as $column => $warnings) {
        if (!isset($all_problems[$line])) {
          $all_problems[$line] = [
            'expected_errors'   => 0,
            'expected_warnings' => 0,
            'found_errors'      => [],
            'found_warnings'    => [],
          ];
        }
        $found_warnings_temp = [];
        foreach ($all_problems[$line]['found_warnings'] as $found_warning) {
          $found_warnings_temp[] = $found_warning;
        }
        $warnings_temp = [];
        foreach ($warnings as $warning) {
          $warnings_temp[] = $warning['message'] . ' (' . $warning['source'] . ')';
        }
        $all_problems[$line]['found_warnings'] = array_merge($found_warnings_temp, $warnings_temp);
      }
      if (isset($expected_warnings[$line])) {
        $all_problems[$line]['expected_warnings'] = $expected_warnings[$line];
      } else {
        $all_problems[$line]['expected_warnings'] = 0;
      }
      unset($expected_warnings[$line]);
    }
    foreach ($expected_warnings as $line => $num_warnings) {
      if (!isset($all_problems[$line])) {
        $all_problems[$line] = [
          'expected_errors'   => 0,
          'expected_warnings' => 0,
          'found_errors'      => [],
          'found_warnings'    => [],
        ];
      }
      $all_problems[$line]['expected_warnings'] = $num_warnings;
    }
    ksort($all_problems);
    foreach ($all_problems as $line => $problems) {
      $num_errors        = count($problems['found_errors']);
      $num_warnings      = count($problems['found_warnings']);
      $expected_errors   = $problems['expected_errors'];
      $expected_warnings = $problems['expected_warnings'];
      $errors      = '';
      $found_string = '';
      if ($expected_errors !== $num_errors || $expected_warnings !== $num_warnings) {
        $line_message     = "[LINE $line]";
        $expected_message = 'Expected ';
        $found_message    = 'in ' . basename($test_file) . ' but found ';
        if ($expected_errors !== $num_errors) {
          $expected_message .= "$expected_errors error(s)";
          $found_message    .= "$num_errors error(s)";
          if ($num_errors !== 0) {
            $found_string .= 'error(s)';
            $errors      .= implode(PHP_EOL . ' -> ', $problems['found_errors']);
          }
          if ($expected_warnings !== $num_warnings) {
            $expected_message .= ' and ';
            $found_message    .= ' and ';
            if ($num_warnings !== 0 && $found_string !== '') {
              $found_string .= ' and ';
            }
          }
        }
        if ($expected_warnings !== $num_warnings) {
          $expected_message .= "$expected_warnings warning(s)";
          $found_message    .= "$num_warnings warning(s)";
          if ($num_warnings !== 0) {
            $found_string .= 'warning(s)';
            if (!empty($errors)) {
              $errors .= PHP_EOL . ' -> ';
            }
            $errors .= implode(PHP_EOL . ' -> ', $problems['found_warnings']);
          }
        }
        $full_message = "$line_message $expected_message $found_message.";
        if ($errors !== '') {
          $full_message .= " The $found_string found were:" . PHP_EOL . " -> $errors";
        }
        $failure_messages[] = $full_message;
      }
    }
    return $failure_messages;
  }

  /**
   * Set a list of CLI values before the file is tested. Overridable.
   *
   * @param string $filename The name of the file being tested.
   * @param Config $config   The config data for the run.
   */
  public function setCliValues(string $filename, Config $config): void {
    return;
  }

  /**
   * Generic data provider for all sniffs: lists all .inc test files in the test directory.
   *
   * @return array<string, array{0: string}>
   */
  public function fixtureProvider(): array {
    $class = new \ReflectionClass($this);
    $dir = dirname($class->getFileName());
    $sniff_name = $class->getShortName();
    $base = $dir . '/' . preg_replace('/UnitTest$/', '', $sniff_name);
    $files = glob($base . '*.inc');
    $data = [];
    foreach ($files as $file) {
      $key = basename($file);
      $data[$key] = [basename($file)];
    }
    return $data;
  }

  /**
   * Runs each fixture individually with clear output (to be used in child classes with @dataProvider fixtureProvider).
   *
   * @param string $fixture_file The name of the fixture file to test.
   */
  public function testFixture(string $fixture_file): void {
    $class = new \ReflectionClass($this);
    $dir = dirname($class->getFileName());
    $test_file = $dir . '/' . $fixture_file;
    $errors = $this->getErrorList($fixture_file);
    $warnings = $this->getWarningList($fixture_file);
    $this->runSniffOnFile($test_file, $errors, $warnings);
  }

  /**
   * Returns the lines where errors should occur.
   *
   * The key of the array should represent the line number and the value
   * should represent the number of errors that should occur on that line.
   *
   * @param string $test_file The name of the file being tested.
   * @return array<int, int>
   */
  abstract protected function getErrorList(string $test_file): array;

  /**
   * Returns the lines where warnings should occur.
   *
   * The key of the array should represent the line number and the value
   * should represent the number of warnings that should occur on that line.
   *
   * @param string $test_file The name of the file being tested.
   * @return array<int, int>
   */
  abstract protected function getWarningList(string $test_file): array;

  /**
   * Returns a list of all test files to check for a given sniff.
   *
   * @param string $test_file_base The base path for the unit test files.
   * @return string[]
   */
  protected function getTestFiles(string $test_file_base): array {
    $test_files = [];
    $dir = substr($test_file_base, 0, strrpos($test_file_base, DIRECTORY_SEPARATOR));
    $di  = new \DirectoryIterator($dir);
    foreach ($di as $file) {
      $path = $file->getPathname();
      if (substr($path, 0, strlen($test_file_base)) === $test_file_base) {
        if ($path !== $test_file_base . 'php' && substr($path, -5) !== 'fixed') {
          $test_files[] = $path;
        }
      }
    }
    sort($test_files);
    return $test_files;
  }

  /**
   * Returns true if this test should be skipped.
   *
   */
  protected function shouldSkipTest(): bool {
    return false;
  }

  /**
   * Returns true if all sniffs should be checked, false for only the current sniff.
   *
   */
  protected function checkAllSniffCodes(): bool {
    return false;
  }

  /**
   * Returns the PHPCS standard (ruleset) to use for this test.
   * Can be overridden in child classes to isolate a specific sniff.
   *
   * By default, returns 'NCAC' (the global standard ruleset).
   *
   * To isolate a sniff (for example, when unit testing a single sniff),
   * it is recommended to override this method in the child test class:
   *
   *   protected function getStandard(): string {
   *     // Use a minimal XML ruleset that only enables the targeted sniff.
   *     // Example of a relative path for an isolated ruleset:
   *     //   tests/NCAC/Formatting/ruleset.formatting.noAlternateControlStructure.xml
   *     return __DIR__ . '/ruleset.formatting.noAlternateControlStructure.xml';
   *   }
   *
   * This ensures that only the sniff under test is executed, without interference from other rules.
   *
   */
  protected function getStandard(): string {
    return 'NCAC';
  }

  /**
   * Runs the sniff on a given file and checks for expected errors/warnings.
   *
   * @param array<int, int> $expected_errors
   * @param array<int, int> $expected_warnings
   */
  protected function runSniffOnFile(string $test_file, array $expected_errors, array $expected_warnings): void {
    $config = new Config(['--standard=' . $this->getStandard()]);
    $ruleset = new Ruleset($config);
    $phpcs_file = new LocalFile($test_file, $ruleset, $config);
    $phpcs_file->process();
    $found_errors = $phpcs_file->getErrors();
    $found_warnings = $phpcs_file->getWarnings();
    // Check for missing expected errors
    foreach ($expected_errors as $line => $count) {
      $this->assertArrayHasKey($line, $found_errors, "Expected error on line $line not found in $test_file");
    }
    // Check for missing expected warnings
    foreach ($expected_warnings as $line => $count) {
      $this->assertArrayHasKey($line, $found_warnings, "Expected warning on line $line not found in $test_file");
    }
    // Check for unexpected errors
    foreach (array_keys($found_errors) as $line) {
      if (!array_key_exists($line, $expected_errors)) {
        $this->fail("Unexpected error found on line $line in $test_file");
      }
    }
    // Check for unexpected warnings
    foreach (array_keys($found_warnings) as $line) {
      if (!array_key_exists($line, $expected_warnings)) {
        $this->fail("Unexpected warning found on line $line in $test_file");
      }
    }
  }

}
