<?php

/**
 * Test PHPCBF fixes with allowDoubleUnderscore and allowLeadingUnderscore enabled.
 * This file tests that the fixer preserves __ and _ when options are enabled.
 * Note: This file is tested with a custom ruleset that enables Drupal options.
 */

// ✅ Should be fixed: camelCase → snake_case
function calculateTotalPrice(float $price, float $tax): float {
  return $price * (1 + $tax);
}

// ✅ Should be fixed: PascalCase → snake_case
function GetUserData(int $userId): array {
  return [];
}

// ✅ Should NOT be modified: double underscores preserved
function mymodule_preprocess_node__homepage(array &$variables): void {
  $variables['data'] = [];
}

// ✅ Should NOT be modified: leading underscore preserved
function _mymodule_internal_helper(string $data): string {
  return strtoupper($data);
}

// ✅ Should be fixed but preserve leading underscore: _camelCase → _snake_case
function _calculatePrice(float $base, float $tax): float {
  return $base * (1 + $tax);
}

// ✅ Should be fixed but preserve double underscores: camelCase__something → snake_case__something
function mymodule_preprocessNode__customPage(array &$variables): void {
  $variables['page'] = 'custom';
}
