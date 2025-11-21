<?php

$result = preg_replace_callback(
  '/pattern/',
  function ($matches) {
    return strtoupper($matches[1]);
  },
  $input
);
