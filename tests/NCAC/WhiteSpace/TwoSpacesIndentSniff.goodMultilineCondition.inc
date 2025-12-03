<?php

// GOOD: Multiline if condition, each line indented +2 spaces from 'if ('
if (
  $foo
  && $bar
  || $baz
) {
  $test = true;
}

// GOOD: Multiline while condition, each line indented +2 spaces from 'while ('
while (
  $cond1
  && $cond2
) {
  $test = false;
}

// GOOD: Multiline for condition, each line indented +2 spaces from 'for ('
for (
  $i = 0;
  $i < 10 && $other_check;
  $i++
) {
  $test = null;
}
