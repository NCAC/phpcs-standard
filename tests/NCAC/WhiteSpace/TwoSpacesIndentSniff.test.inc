<?php // CECI EST LA LIGNE 1 !!!
// Cas 1 : Bloc simple
if ($foo) {
  doSomething();
}

// Cas 2 : Bloc imbriqué
if ($foo) {
  if ($bar) {
    doSomething();
  }
}

// Cas 3 : else/elseif
if ($foo) {
  doA();
} else if ($bar) {
  doB();
} else {
  doC();
}

// Cas 4 : } else { sur la même ligne
if ($foo) {
  doA();
} else {
  doB();
}

// Cas 5 : switch/case
switch ($var) {
  case 1:
    doA();
    break;

  case 2:
    doB();
    break;

  default:
    doDefault();
    break;
}

// Cas 6 : tableau simple
$array = [
  1,
  2,
  3,
];

// Cas 7 : tableau imbriqué
$array = [
  'a' => [
    1,
    2,
  ],
  'b' => 3,
];

// Cas 8 : assignation multilignes
$foo =
  $bar
  && $baz;

// Cas 9 : parenthèses multilignes
$result = (
  $a
  || $b
);

// Cas 10 : fonction avec arguments multilignes
function foo(
  $a,
  $b
) {
  return $a + $b;
}

/**
 * Ceci est un docblock
 * @param string $foo
 */
function bar($foo) {
  return $foo;
}

$result = $cond
  ? $a
  : $b;

$result = $cond1
  ? $a
  : ($cond2
    ? $b
    : $c);

function baz() {
  return [
    'x' => 1,
    'y' => 2,
  ];
}

if ($foo) {
  doA();
} else if ($bar) {
  doB();
} else {
  doC();
}

// Cas 18 : tableau, parenthèses et accolades imbriqués
$complex = [
  'a' => [
    'b' => function ($x) {
      return [
        $x,
      ];
    },
  ],
];

// Cas 19 : switch imbriqué
switch ($a) {
  case 1:
    switch ($b) {
      case 2:
        doSomething();
        break;

      default:
        // intentionally left blank
        break;
    }
    break;

  default:
    // intentionally left blank
    break;
}

// Cas 20 : ligne vide

// Cas 21 : commentaire seul
// Ceci est un commentaire

// Cas 22 : code après fermeture
if ($foo) {
  doA();
}
doB();
