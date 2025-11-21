<?php

// GOOD: Arrow function not indented properly
$mapper = fn($item) => [
  'id' => $item->id,        // Should be +2 spaces from array opening
  'name' => $item->name     // Should be +2 spaces from array opening
];

// GOOD: Arrow function in array_map - wrong indentation
$results = array_map(
  fn($user) => [
    'id' => $user->getId(),  // Should be +4 spaces (2 for fn + 2 for array)
    'email' => $user->getEmail()
  ],
  $users
);

// GOOD: Nested arrow functions wrong indentation
$complex = fn($items) => array_filter(
  array_map(                    // Should be +2 spaces from fn opening
    fn($item) => $item->isActive() // Should be +4 spaces
      ? [
        'id' => $item->id,           // Should be +6 spaces
        'status' => 'active'
      ]
      : null,
    $items
  ),
  fn($result) => $result !== null   // Should be +2 spaces from array_filter
);

// GOOD: Arrow function with method chaining wrong indentation
$processor = fn($data) => $this->validator
  ->validateInput($data)    // Should be +2 spaces from fn body
  ->transformToArray()      // Should be +2 spaces from fn body
  ->process();

// GOOD: Arrow function in ternary
$transformer = $useComplex
  ? fn($data) => $this->complexTransformer
    ->withOptions(['strict' => true])  // Should be +4 spaces (2 for ternary + 2 for chaining)
    ->transform($data)
  : fn($data) => [
    'raw' => $data,          // Should be +4 spaces (2 for ternary + 2 for array)
    'processed' => false
  ];


// GOOD: Arrow function returning match expression wrong indentation
$statusMapper = fn($code) => match($code) {
  200 => 'success',        // Should be +2 spaces from match opening
  404 => 'not_found',
  default => 'unknown'
};
