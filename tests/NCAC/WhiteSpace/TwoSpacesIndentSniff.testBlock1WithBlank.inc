<?php


// GOOD: Closure in preg_replace_callback with proper indentation
$result = preg_replace_callback(
  '/pattern/',
  function ($matches) {
    return strtoupper($matches[1]);
  },
  $input
);
