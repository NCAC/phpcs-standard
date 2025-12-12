<?php

// BAD: Ternary branches not properly indented (should be +2 spaces relative to the assignment)
$foo = $bar
? 'a'
: 'b';

// BAD: Ternary in multiline parenthesis, branches not indented
$result = (
$cond
? 'x'
: 'y'
);

// BAD: nested ternary (deprecated in PHP 7.4+)
// This construct is deprecated and should be avoided.
// See: https://www.php.net/manual/en/migration74.deprecated.php#migration74.deprecated.core.nested-ternary
$test = $cond1
? 'choice_a'
: ($cond2
? 'choice_b'
: 'choice_c');

function test() {}
