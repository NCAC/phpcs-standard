<?php

// GOOD: Closure in preg_replace_callback with proper indentation
$result = preg_replace_callback(
  '/pattern/',
  function ($matches) {
    return strtoupper($matches[1]);
  },
  $input
);

// GOOD: Closure with multiple statements
$result = preg_replace_callback(
  '/pattern/',
  function ($matches) {
    $value = $matches[1];
    return strtoupper($value);
  },
  $input
);

// GOOD: Nested closure in array_map
$mapped = array_map(
  function ($item) {
    return $item * 2;
  },
  $array
);

// GOOD: Closure with complex logic
$result = preg_replace_callback(
  '/_([a-z])/',
  function ($matches) {
    if (isset($matches[1])) {
      return strtoupper($matches[1]);
    }
    return '';
  },
  $string
);

// GOOD: Closure assigned to variable with proper indentation
$callback = function ($x) {
  return $x * 2;
};
