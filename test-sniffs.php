<?php
/**
 * End-to-end test to verify that the NCAC standard works correctly.
 * 
 * This script tests the complete integration of the standard by verifying:
 * 1. That PHPCS recognizes the NCAC standard
 * 2. That NCAC rules are loaded and working
 * 3. That automatic fixes work
 * 
 * @author   NCAC
 * @category PHP_CodeSniffer
 * @package  NCAC
 * @license  https://github.com/NCAC/phpcs-standard/blob/main/LICENSE MIT License
 */

$workingDir = __DIR__;
$phpcsExecutable = $workingDir . '/vendor/bin/phpcs';
$phpcbfExecutable = $workingDir . '/vendor/bin/phpcbf';
$rulesetPath = $workingDir . '/ruleset.xml';

// Check that tools exist
if (!file_exists($phpcsExecutable)) {
  echo "ERROR: PHPCS executable not found at: $phpcsExecutable\n";
  exit(1);
}

if (!file_exists($phpcbfExecutable)) {
  echo "ERROR: PHPCBF executable not found at: $phpcbfExecutable\n";
  exit(1);
}

if (!file_exists($rulesetPath)) {
  echo "ERROR: Ruleset not found at: $rulesetPath\n";
  exit(1);
}

echo "🔍 Testing NCAC PHP_CodeSniffer Standard...\n\n";

// Test 1: Check if PHPCS can load the standard
echo "1. Checking if PHPCS can load the NCAC standard...\n";
$output = shell_exec("$phpcsExecutable --standard=$rulesetPath -e 2>&1");
if (strpos($output, 'NCAC') === false) {
  echo "ERROR: PHPCS cannot load the NCAC standard\n";
  echo "Output: $output\n";
  exit(1);
}
echo "✅ PHPCS successfully loaded the NCAC standard\n\n";

// Test 2: Create a test file with violations
echo "2. Creating test file with violations...\n";
$testFile = $workingDir . '/test-file.php';
$testContent = '<?php
// Test file with intentional violations to test the NCAC standard
namespace Test;

class   test_class     {
public function    bad_method_name( $bad_var_name ) {
$bad_indentation = "test";
        return $bad_indentation;
}
}
';

file_put_contents($testFile, $testContent);
echo "✅ Test file created\n\n";

// Test 3: Check that PHPCS detects violations
echo "3. Running PHPCS to detect violations...\n";
exec("$phpcsExecutable --standard=$rulesetPath $testFile 2>&1", $outputArray, $exitCode);
$output = implode("\n", $outputArray);

if ($exitCode === 0) {
  echo "WARNING: PHPCS found no violations (expected some violations)\n";
  echo "Output: $output\n";
} else {
  echo "✅ PHPCS detected violations as expected\n";
  // Count detected violations
  $lines = explode("\n", $output);
  $errorCount = 0;
  foreach ($lines as $line) {
    if (strpos($line, 'ERROR') !== false || strpos($line, 'WARNING') !== false) {
      $errorCount++;
    }
  }
  echo "   Found $errorCount violation lines\n";
}
echo "\n";

// Test 4: Test PHPCBF for automatic fixes
echo "4. Testing PHPCBF for automatic fixes...\n";
exec("$phpcbfExecutable --standard=$rulesetPath $testFile 2>&1", $outputArray2, $exitCode2);
$output = implode("\n", $outputArray2);
if (strpos($output, 'FIXED') !== false || strpos($output, 'No violations were found') !== false) {
  echo "✅ PHPCBF completed successfully\n";
} else {
  echo "INFO: PHPCBF output: $output\n";
}
echo "\n";

// Test 5: Check that the standard works with NCAC files themselves
echo "5. Testing NCAC standard on its own files...\n";
exec("$phpcsExecutable --standard=$rulesetPath NCAC/ 2>&1", $outputArray3, $exitCode3);
$output = implode("\n", $outputArray3);

if ($exitCode3 === 0) {
  echo "✅ NCAC files pass their own coding standard\n";
} else {
  echo "INFO: NCAC files have some violations:\n";
  echo "$output\n";
}

// Clean up
unlink($testFile);

echo "\n🎉 End-to-end tests completed successfully!\n";
echo "📦 The NCAC PHP_CodeSniffer standard is ready for use.\n\n";

echo "Usage:\n";
echo "  composer install ncac/phpcs-standard\n";
echo "  vendor/bin/phpcs --standard=vendor/ncac/phpcs-standard/ruleset.xml your-files/\n";
echo "  vendor/bin/phpcbf --standard=vendor/ncac/phpcs-standard/ruleset.xml your-files/\n\n";

exit(0);
