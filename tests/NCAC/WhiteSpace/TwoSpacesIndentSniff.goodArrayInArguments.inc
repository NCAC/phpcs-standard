<?php

/**
 * Function with array argument.
 * 
 * @param array<string, mixed> $args
 * @return array<string, mixed>
 */
function test_array_arguments(array $args): array {
  return $args;
}



$local_array = test_array_arguments([
  'key1' => 'value1',
  'key2' => 42,
  'key3' => [1, 2, 3],
]);
