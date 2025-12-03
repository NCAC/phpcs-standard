<?php
// Comment on line 2
$result = preg_replace_callback(
  '/pattern/',
  function ($matches) {
    return strtoupper($matches[1]);
  },
  $input
);
