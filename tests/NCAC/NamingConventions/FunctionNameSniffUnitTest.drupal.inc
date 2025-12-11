<?php

/**
 * Valid functions when allowDoubleUnderscore and allowLeadingUnderscore are enabled.
 * Note: This file is tested with a custom ruleset that enables Drupal options.
 * See: ruleset.namingConventions.functionName.drupal.xml
 */

// ✅ Valid Drupal preprocess hooks with double underscores
function mymodule_preprocess_node__homepage(array &$variables): void {
  $variables['data'] = [];
}

function theme_preprocess_paragraph__chapitres(array &$variables): void {
  $variables['chapters'] = [];
}

function conseil_preprocess_node__article_avec_sommaire(array &$variables): void {
  $variables['summary'] = true;
}

// ✅ Valid Drupal theme suggestions hooks
function mymodule_theme_suggestions_node__alter(array &$suggestions, array $variables): void {
  $suggestions[] = 'node__custom';
}

function mymodule_theme_suggestions_paragraph__alter(array &$suggestions): void {
  $suggestions[] = 'paragraph__custom';
}

// ✅ Valid internal/private functions with leading underscore
function _mymodule_internal_helper(string $data): string {
  return strtoupper($data);
}

function _another_private_function(): void {
  // Private function
}

function _process_data(array $items): array {
  return array_map('strtolower', $items);
}

// ✅ Valid standard snake_case functions
function calculate_total_price(float $price, float $tax): float {
  return $price * (1 + $tax);
}

function get_user_data(int $user_id): array {
  return [];
}

function format_currency(float $amount): string {
  return '$' . number_format($amount, 2);
}

// ✅ Valid all-lowercase functions
function test(): void {
  echo 'test';
}

function data(): array {
  return [];
}
