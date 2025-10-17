<?php

// BAD: Multiline if condition, lines not indented
if (
$foo
&& $bar
|| $baz
) {
  doSomething();
}

// BAD: Multiline while condition, lines not indented
while (
$cond1
&& $cond2
) {
  doSomethingElse();
}

// BAD: Multiline for condition, lines not indented
for (
$i = 0;
$i < 10 && $otherCheck;
$i++
) {
  loopBody();
}
