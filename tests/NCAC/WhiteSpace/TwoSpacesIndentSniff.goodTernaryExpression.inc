<?php

// GOOD: Ternary branches properly indented (+2 spaces)
$foo = $bar
  ? 'a'
  : 'b';

// GOOD: Ternary in multiline parenthesis, branches properly indented
$result = (
  $cond
    ? 'x'
    : 'y'
);
