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
