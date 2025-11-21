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

// GOOD: Nested ternary expressions, all branches properly indented
$value = $a
  ? ($b
    ? 'first'
    : 'second')
  : 'third';
$other_assignment = null;

$other_value = $x
  ? 'yes'
  : ($y
    ? 'maybe'
    : 'no');
$final = null;
