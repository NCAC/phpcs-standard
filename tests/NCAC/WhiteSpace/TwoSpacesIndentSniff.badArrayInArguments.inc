<?php
/**
 * Function with array argument - BAD indentation examples.
 * This file contains intentionally bad indentation for testing purposes.
 * Do not auto-format this file.
 * 
 * @param array<string, mixed> $args
 * @return array<string, mixed>
 */
function test_array_arguments(array $args): array {
  return $args;
}

// Error 1: Array elements should be indented with 2 spaces, not 4
$bad_array_1 = test_array_arguments([
    'key1' => 'value1',
    'key2' => 42,
    'key3' => [1, 2, 3],
]);

// Error 2: Array elements should be indented with 2 spaces, not 1
$bad_array_2 = test_array_arguments([
 'key1' => 'value1',
 'key2' => 42,
]);

// Error 3: Mixed indentation - inconsistent spacing
$bad_array_3 = test_array_arguments([
  'key1' => 'value1',
    'key2' => 42,
   'key3' => [
     'nested1' => 'value',
      'nested2' => 'value',
   ],
]);

// Error 4: No indentation for array elements
$bad_array_4 = test_array_arguments([
'key1' => 'value1',
'key2' => 42,
'key3' => [
'nested' => 'value',
],
]);
