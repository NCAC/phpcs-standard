<?php

/**
 * E2E test for Drupal hook naming conventions.
 * 
 * This test validates that the FunctionNameSniff correctly handles Drupal
 * preprocess hooks and other functions with double underscores when the
 * allowDoubleUnderscore option is enabled.
 */

namespace NCAC\E2ETests;

class DrupalHooksTest extends E2ETest {

  public function getName(): string {
    return "Drupal Hooks Naming Test";
  }

  public function run(): void {
    // Test 1: Drupal hooks should fail without the option
    $this->step("Testing Drupal hooks WITHOUT allowDoubleUnderscore...");

    $drupal_content = '<?php

function mymodule_preprocess_node__homepage(array &$variables): void {
  $variables["data"] = "value";
}

function _internal_helper(string $data): string {
  return $data;
}
';

    $file1 = $this->runner->createTestFile('drupal-hooks-no-option.php', $drupal_content);
    $result1 = $this->runner->runPhpcs($file1, ['sniffs' => 'NCAC.NamingConventions.FunctionName']);

    // Should detect errors for double underscore and leading underscore
    $has_double_underscore_error = false;
    $has_leading_underscore_error = false;

    foreach ($result1['lines'] as $line) {
      if (strpos($line, 'mymodule_preprocess_node__homepage') !== false && strpos($line, 'snake_case') !== false) {
        $has_double_underscore_error = true;
      }
      if (strpos($line, '_internal_helper') !== false && strpos($line, 'snake_case') !== false) {
        $has_leading_underscore_error = true;
      }
    }

    $this->runner->assertTrue(
      $has_double_underscore_error,
      "Without allowDoubleUnderscore, double underscore hooks should trigger error"
    );

    $this->runner->assertTrue(
      $has_leading_underscore_error,
      "Without allowLeadingUnderscore, leading underscore functions should trigger error"
    );

    $this->success("Correctly detected invalid naming without Drupal options");

    // Test 2: Create custom ruleset with Drupal options
    $this->step("Creating Drupal-compatible ruleset...");

    $ruleset_content = '<?xml version="1.0"?>
<ruleset name="Drupal Test">
  <rule ref="NCAC">
    <exclude name="NCAC.Formatting.OpeningBraceKAndR"/>
    <exclude name="NCAC.WhiteSpace.TwoSpacesIndent"/>
  </rule>
  <rule ref="NCAC.NamingConventions.FunctionName">
    <properties>
      <property name="allowDoubleUnderscore" value="1"/>
      <property name="allowLeadingUnderscore" value="1"/>
    </properties>
  </rule>
</ruleset>';

    // Create ruleset in tmp directory
    $tmp_dir = dirname($this->runner->createTestFile('dummy.txt', 'test'));
    $ruleset_file = $tmp_dir . '/drupal-ruleset.xml';
    file_put_contents($ruleset_file, $ruleset_content);

    $this->success("Drupal ruleset created");

    // Test 3: Drupal hooks should pass with options enabled
    $this->step("Testing Drupal hooks WITH allowDoubleUnderscore...");

    $file3 = $this->runner->createTestFile('drupal-hooks-with-option.php', $drupal_content);

    // Use runPhpcs but with custom standard
    $result3 = $this->runner->runPhpcs($file3, [
      'standard' => $ruleset_file,
      'sniffs' => 'NCAC.NamingConventions.FunctionName',
    ]);

    // Should NOT detect errors
    $has_errors = false;
    foreach ($result3['lines'] as $line) {
      if (strpos($line, 'ERROR') !== false && strpos($line, 'snake_case') !== false) {
        $has_errors = true;
        break;
      }
    }

    $this->runner->assertFalse(
      $has_errors,
      "With Drupal options enabled, hooks should NOT trigger errors"
    );

    $this->success("Drupal hooks correctly accepted with options enabled");

    // Test 4: PHPCBF should preserve double underscores
    $this->step("Testing PHPCBF preservation of double underscores...");

    $mixed_content = '<?php

// Should be fixed
function calculateTotalPrice() {
  return 100;
}

// Should NOT be modified
function mymodule_preprocess_node__homepage(array &$variables): void {
  $variables["test"] = "value";
}

function _internal_helper() {
  return "data";
}
';

    $file4 = $this->runner->createTestFile('drupal-phpcbf-test.php', $mixed_content);
    $original_content = file_get_contents($file4);

    // Run PHPCBF with Drupal ruleset
    $result4 = $this->runner->runPhpcbf($file4, [
      'standard' => $ruleset_file,
      'sniffs' => 'NCAC.NamingConventions.FunctionName',
    ]);

    $fixed_content = file_get_contents($file4);

    // Check that calculateTotalPrice was fixed
    $this->runner->assertTrue(
      strpos($fixed_content, 'calculate_total_price') !== false,
      "PHPCBF should fix camelCase function"
    );

    // Check that double underscores were preserved
    $this->runner->assertTrue(
      strpos($fixed_content, 'mymodule_preprocess_node__homepage') !== false,
      "PHPCBF should preserve double underscores in Drupal hooks"
    );

    // Check that leading underscore was preserved
    $this->runner->assertTrue(
      strpos($fixed_content, '_internal_helper') !== false,
      "PHPCBF should preserve leading underscores"
    );

    $this->success("PHPCBF correctly preserved Drupal hooks while fixing other issues");
  }

}
