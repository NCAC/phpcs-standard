<?php

/**
 * Invalid functions when allowDoubleUnderscore and allowLeadingUnderscore are disabled (default).
 * This tests that the sniff correctly rejects Drupal-style function names when options are off.
 */

// ❌ Invalid: double underscores not allowed by default
function mymodule_preprocess_node__homepage(array &$variables): void {
  $variables['data'] = [];
}

function theme_preprocess_paragraph__chapitres(array &$variables): void {
  $variables['chapters'] = [];
}

// ❌ Invalid: leading underscore not allowed by default
function _mymodule_internal_helper(string $data): string {
  return strtoupper($data);
}

function _another_private_function(): void {
  // Private function
}

// ✅ Valid: standard snake_case without double underscores or leading underscores
function calculate_total_price(float $price, float $tax): float {
  return $price * (1 + $tax);
}

function get_user_data(int $user_id): array {
  return [];
}
