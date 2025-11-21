<?php

function test_function( // stack: [] → [LIST_MULTILINE], indent: 0
  array $arg1, // stack: [LIST_MULTILINE], indent: 1
  ?callable $arg2 = null, // stack: [LIST_MULTILINE], indent: 1
  int $arg3 = 42 // stack: [LIST_MULTILINE], indent: 1
) { // stack: [LIST_MULTILINE] → [] → [BLOCK], indent: 0
    $temp_value = $arg2 // stack: [BLOCK], indent: 1
      ? $arg2($arg1) // stack: [BLOCK] → [BLOCK, TERNARY_OPERATOR], indent: 2
      : null; // stack: [BLOCK, TERNARY_OPERATOR] → [BLOCK], indent: 2
    foreach ($arg1 as $item) { // stack: [BLOCK] → [BLOCK, BLOCK], indent: 1
      $result = $item > $arg3 // stack: [BLOCK, BLOCK], indent: 2
        ? 'greater' // stack: [BLOCK, BLOCK] → [BLOCK, BLOCK, TERNARY_OPERATOR], indent: 3
        : 'lesser or equal'; // stack: [BLOCK, BLOCK, TERNARY_OPERATOR] → [BLOCK, BLOCK], indent: 3
    } // stack: [BLOCK, BLOCK] → [BLOCK], indent: 1
} // stack: [BLOCK] → [], indent: 0

// Assignation multiligne avec ternaires imbriquées
$result = $condition1 // stack: [] → [TERNARY_OPERATOR], indent: 0
  ? ($condition2 // stack: [TERNARY_OPERATOR] → [TERNARY_OPERATOR, TERNARY_OPERATOR], indent: 1
      ? $valueA // stack: [TERNARY_OPERATOR, TERNARY_OPERATOR], indent: 2
      : $valueB) // stack: [TERNARY_OPERATOR, TERNARY_OPERATOR] → [TERNARY_OPERATOR], indent: 2
  : ($condition3 // stack: [TERNARY_OPERATOR] → [TERNARY_OPERATOR, TERNARY_OPERATOR], indent: 1
      ? $valueC // stack: [TERNARY_OPERATOR, TERNARY_OPERATOR], indent: 2
      : $valueD); // stack: [TERNARY_OPERATOR, TERNARY_OPERATOR] → [], indent: 2
$test = null; // stack: [], indent: 0

// Méthodes chaînées sur plusieurs lignes
$object // stack: [] → [CHAINED_BLOCK], indent: 0
  ->setValue('foo') // stack: [CHAINED_BLOCK], indent: 1
  ->filter(function ($item) { // stack: [CHAINED_BLOCK] → [CHAINED_BLOCK, BLOCK], indent: 2
    return $item > 0; // stack: [CHAINED_BLOCK, BLOCK], indent: 3
  }) // stack: [CHAINED_BLOCK, BLOCK] → [CHAINED_BLOCK], indent: 2
  ->map(fn($x) => $x * 2) // stack: [CHAINED_BLOCK], indent: 1
  ->finalize(); // stack: [CHAINED_BLOCK] → [], indent: 1
$test = null; // stack: [], indent: 0

// array_map + array_filter imbriqués
$filtered = array_map( // stack: [] → [LIST_MULTILINE], indent: 0
  fn($x) => [ // stack: [LIST_MULTILINE] → [LIST_MULTILINE, LIST_MULTILINE], indent: 1
    'original' => $x, // stack: [LIST_MULTILINE, LIST_MULTILINE], indent: 2
    'filtered' => array_filter( // stack: [LIST_MULTILINE, LIST_MULTILINE] → [LIST_MULTILINE, LIST_MULTILINE, LIST_MULTILINE], indent: 2
      $x['values'], // stack: [LIST_MULTILINE, LIST_MULTILINE, LIST_MULTILINE], indent: 3
      fn($v) => $v > 10 // stack: [LIST_MULTILINE, LIST_MULTILINE, LIST_MULTILINE], indent: 3
    ), // stack: [LIST_MULTILINE, LIST_MULTILINE, LIST_MULTILINE] → [LIST_MULTILINE, LIST_MULTILINE], indent: 2
  ], // stack: [LIST_MULTILINE, LIST_MULTILINE] → [LIST_MULTILINE], indent: 1
  $inputList // stack: [LIST_MULTILINE], indent: 1
); // stack: [LIST_MULTILINE] → [], indent: 0

// Closure imbriquée dans une assignation
$callback = function ($a) { // stack: [] → [BLOCK], indent: 0
  return function ($b) use ($a) { // stack: [BLOCK] → [BLOCK, BLOCK], indent: 1
    return $a + $b; // stack: [BLOCK, BLOCK], indent: 2
  }; // stack: [BLOCK, BLOCK] → [BLOCK], indent: 1
}; // stack: [BLOCK] → [], indent: 0

// @to continue
// Appel de fonction avec tableau et closure multilignes
processData( // stack: [] → [LIST_MULTILINE], indent: 0
  [ // stack: [LIST_MULTILINE] → [LIST_MULTILINE, LIST_MULTILINE], indent: 1
    'foo' => 1, // stack: [LIST_MULTILINE, LIST_MULTILINE], indent: 2
    'bar' => 2, // stack: [LIST_MULTILINE, LIST_MULTILINE], indent: 2
    'baz' => [ // stack: [LIST_MULTILINE, LIST_MULTILINE] → [LIST_MULTILINE, LIST_MULTILINE, LIST_MULTILINE], indent: 2
      10, // stack: [LIST_MULTILINE, LIST_MULTILINE, LIST_MULTILINE], indent: 3
      20, // stack: [LIST_MULTILINE, LIST_MULTILINE, LIST_MULTILINE], indent: 3
      30, // stack: [LIST_MULTILINE, LIST_MULTILINE, LIST_MULTILINE], indent: 3
    ], // stack: [LIST_MULTILINE, LIST_MULTILINE, LIST_MULTILINE] → [LIST_MULTILINE, LIST_MULTILINE], indent: 2
  ], // stack: [LIST_MULTILINE, LIST_MULTILINE] → [LIST_MULTILINE], indent: 1
  function ($item) { // stack: [LIST_MULTILINE] → [LIST_MULTILINE, BLOCK], indent: 1
    if ($item > 10) { // stack: [LIST_MULTILINE, BLOCK] → [LIST_MULTILINE, BLOCK, BLOCK], indent: 2
      return $item * 2; // stack: [LIST_MULTILINE, BLOCK, BLOCK], indent: 3
    } // stack: [LIST_MULTILINE, BLOCK], indent: 2
    return $item; // stack: [LIST_MULTILINE, BLOCK], indent: 2
  } // stack: [LIST_MULTILINE, BLOCK] → [LIST_MULTILINE], indent: 1
); // stack: [LIST_MULTILINE] → [], indent: 0

// Structure de contrôle imbriquée
foreach ($list as $element) { // stack: [] → [BLOCK], indent: 0
  if ($element['active']) { // stack: [BLOCK] → [BLOCK, BLOCK], indent: 1
    $value = $element['data'] // stack: [BLOCK, BLOCK] → [BLOCK, BLOCK, TERNARY_OPERATOR], indent: 2
      ? ($element['data']['score'] > 50 // stack: [BLOCK, BLOCK, TERNARY_OPERATOR] → [BLOCK, BLOCK, TERNARY_OPERATOR, TERNARY_OPERATOR], indent: 3
          ? 'high' // stack: [BLOCK, BLOCK, TERNARY_OPERATOR, TERNARY_OPERATOR], indent: 4
          : 'low') // stack: [BLOCK, BLOCK, TERNARY_OPERATOR, TERNARY_OPERATOR] → [BLOCK, BLOCK, TERNARY_OPERATOR], indent: 4
      : 'none'; // stack: [BLOCK, BLOCK, TERNARY_OPERATOR] → [BLOCK, BLOCK], indent: 3
  } // stack: [BLOCK, BLOCK] → [BLOCK], indent: 1
} // stack: [BLOCK] → [], indent: 0

// Switch imbriqué avec assignation et ternaires
switch ($type) { // stack: [] → [SWITCH_BLOCK], indent: 0
  case 'A': // stack: [SWITCH_BLOCK] → [SWITCH_BLOCK, CASE_BLOCK], indent: 1
    $output = $flag // stack: [SWITCH_BLOCK, CASE_BLOCK] → [SWITCH_BLOCK, CASE_BLOCK, TERNARY_OPERATOR], indent: 2
      ? ($extra ? 'X' : 'Y') // stack: [SWITCH_BLOCK, CASE_BLOCK, TERNARY_OPERATOR] → [SWITCH_BLOCK, CASE_BLOCK, TERNARY_OPERATOR, TERNARY_OPERATOR], indent: 3
      : 'Z'; // stack: [SWITCH_BLOCK, CASE_BLOCK, TERNARY_OPERATOR, TERNARY_OPERATOR] → [SWITCH_BLOCK, CASE_BLOCK], indent: 3
    break; // stack: [SWITCH_BLOCK, CASE_BLOCK], indent: 2
  case 'B': // stack: [SWITCH_BLOCK] → [SWITCH_BLOCK, CASE_BLOCK], indent: 1
    $output = array_map( // stack: [SWITCH_BLOCK, CASE_BLOCK] → [SWITCH_BLOCK, CASE_BLOCK, LIST_MULTILINE], indent: 2
      fn($v) => $v > 0 ? $v : null, // stack: [SWITCH_BLOCK, CASE_BLOCK, LIST_MULTILINE], indent: 3
      $values // stack: [SWITCH_BLOCK, CASE_BLOCK, LIST_MULTILINE], indent: 3
    ); // stack: [SWITCH_BLOCK, CASE_BLOCK, LIST_MULTILINE] → [SWITCH_BLOCK, CASE_BLOCK], indent: 2
    break; // stack: [SWITCH_BLOCK, CASE_BLOCK], indent: 2
} // stack: [SWITCH_BLOCK] → [], indent: 0

// Appel de méthode avec arguments multilignes et closure
$service->run( // stack: [] → [CHAINED_BLOCK], indent: 0
  $param1, // stack: [CHAINED_BLOCK] → [CHAINED_BLOCK, LIST_MULTILINE], indent: 1
  $param2, // stack: [CHAINED_BLOCK, LIST_MULTILINE], indent: 1
  function ($x) { // stack: [CHAINED_BLOCK, LIST_MULTILINE] → [CHAINED_BLOCK, LIST_MULTILINE, BLOCK], indent: 1
    return $x ? $x * 2 : null; // stack: [CHAINED_BLOCK, LIST_MULTILINE, BLOCK], indent: 2
  } // stack: [CHAINED_BLOCK, LIST_MULTILINE, BLOCK] → [CHAINED_BLOCK, LIST_MULTILINE], indent: 1
);

// Array associatif multilignes
$config = [ // stack: [] → [LIST_MULTILINE], indent: 0
  'host' => 'localhost', // stack: [LIST_MULTILINE], indent: 1
  'port' => 3306, // stack: [LIST_MULTILINE], indent: 1
  'options' => [ // stack: [LIST_MULTILINE] → [LIST_MULTILINE, LIST_MULTILINE], indent: 1
    'persistent' => true, // stack: [LIST_MULTILINE, LIST_MULTILINE], indent: 2
    'timeout' => 30, // stack: [LIST_MULTILINE, LIST_MULTILINE], indent: 2
  ], // stack: [LIST_MULTILINE, LIST_MULTILINE] → [LIST_MULTILINE], indent: 1
]; // stack: [LIST_MULTILINE] → [], indent: 0

// Expression complexe avec parenthèses multilignes
$complex = ( // stack: [] → [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 0
  $a && $b // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 1
    ? $c // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK] → [PARENTHESIS_MULTILINE_CONTROL_BLOCK, TERNARY_OPERATOR], indent: 2
    : $d // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK, TERNARY_OPERATOR], indent: 2
) + ( // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK, TERNARY_OPERATOR] → [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 0
  $e // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 1
    ? $f // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK] → [PARENTHESIS_MULTILINE_CONTROL_BLOCK, TERNARY_OPERATOR], indent: 2
    : $g // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK, TERNARY_OPERATOR], indent: 2
); // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK, TERNARY_OPERATOR] → [], indent: 0

// Fonction anonyme imbriquée dans un tableau
$list = [ // stack: [] → [LIST_MULTILINE], indent: 0
  fn($x) => fn($y) => $x + $y, // stack: [LIST_MULTILINE], indent: 1
  fn($z) => $z * 2, // stack: [LIST_MULTILINE], indent: 1
]; // stack: [LIST_MULTILINE] → [], indent: 0

// Appel de fonction avec arguments et tableau imbriqué
call( // stack: [] → [LIST_MULTILINE], indent: 0
  'action', // stack: [LIST_MULTILINE], indent: 1
  [ // stack: [LIST_MULTILINE] → [LIST_MULTILINE, LIST_MULTILINE], indent: 2
    'params' => [ // stack: [LIST_MULTILINE, LIST_MULTILINE] → [LIST_MULTILINE, LIST_MULTILINE, LIST_MULTILINE], indent: 3
      'foo' => 'bar', // stack: [LIST_MULTILINE, LIST_MULTILINE, LIST_MULTILINE], indent: 4
      'baz' => [1, 2, 3], // stack: [LIST_MULTILINE, LIST_MULTILINE, LIST_MULTILINE], indent: 4
    ], // stack: [LIST_MULTILINE, LIST_MULTILINE, LIST_MULTILINE] → [LIST_MULTILINE, LIST_MULTILINE], indent: 3
    'callback' => fn($v) => $v ? $v : null, // stack: [LIST_MULTILINE, LIST_MULTILINE], indent: 3
  ] // stack: [LIST_MULTILINE, LIST_MULTILINE] → [LIST_MULTILINE], indent: 2
); // stack: [LIST_MULTILINE] → [], indent: 0

// If imbriqué avec assignation et méthode chaînée
if ( // stack: [] → [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 0
  $user->isActive() // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 1
    && $user->getProfile() // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 2
      ->hasPermission('edit') // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK] → [PARENTHESIS_MULTILINE_CONTROL_BLOCK, CHAINED_BLOCK], indent: 3
) { // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK, CHAINED_BLOCK] → [BLOCK], indent: 0
  $user->updateProfile( // stack: [BLOCK] → [BLOCK, CHAINED_BLOCK], indent: 1
    [ // stack: [BLOCK, CHAINED_BLOCK] → [BLOCK, CHAINED_BLOCK, LIST_MULTILINE], indent: 2
      'lastEdit' => time(), // stack: [BLOCK, CHAINED_BLOCK, LIST_MULTILINE], indent: 3
      'status' => 'updated', // stack: [BLOCK, CHAINED_BLOCK, LIST_MULTILINE], indent: 3
    ] // stack: [BLOCK, CHAINED_BLOCK, LIST_MULTILINE] → [BLOCK, CHAINED_BLOCK], indent: 2
  ); // stack: [BLOCK, CHAINED_BLOCK] → [BLOCK], indent: 1
} // stack: [BLOCK] → [], indent: 0

// For avec parenthèses multilignes
for ( // stack: [] → [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 0
  $i = 0; // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 1
  $i < count($array); // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 1
  $i++ // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 1
) { // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK] → [BLOCK], indent: 0
  $array[$i] = $i * 2; // stack: [BLOCK], indent: 1
} // stack: [BLOCK] → [], indent: 0

// While avec condition multilignes
while ( // stack: [] → [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 0
  $foo // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 1
    && $bar // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 2
    && ( // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK] → [PARENTHESIS_MULTILINE_CONTROL_BLOCK, PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 3
      $baz > 0 // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK, PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 4
      || $qux < 10 // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK, PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 4
    ) // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK, PARENTHESIS_MULTILINE_CONTROL_BLOCK] → [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 3
) { // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK] → [BLOCK], indent: 0
  doSomething(); // stack: [BLOCK], indent: 1
} // stack: [BLOCK] → [], indent: 0

// foreach avec closure multilignes
foreach ( // stack: [] → [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 0
  array_filter( // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK] → [PARENTHESIS_MULTILINE_CONTROL_BLOCK, CHAINED_BLOCK], indent: 1
    $items, // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK, CHAINED_BLOCK], indent: 2
    fn($item) => $item['active'] // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK, CHAINED_BLOCK], indent: 2
  ) as $activeItem // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK, CHAINED_BLOCK] → [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 1
) { // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK] → [BLOCK], indent: 0
  process($activeItem); // stack: [BLOCK], indent: 1
} // stack: [BLOCK] → [], indent: 0

// Méthode chaînée avec null-safe et closure
$score = $user?->getProfile()?->getScores()?->filter( // stack: [] → [CHAINED_BLOCK], indent: 0
  fn($s) => $s > 50 // stack: [CHAINED_BLOCK], indent: 1
)?->first() ?? 0; // stack: [CHAINED_BLOCK] → [], indent: 0

// Null-safe dans une structure de contrôle
if ( // stack: [] → [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 0
  $user // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK], indent: 1
    ?->getProfile() // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK] → [PARENTHESIS_MULTILINE_CONTROL_BLOCK, CHAINED_BLOCK], indent: 2
    ?->isActive() // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK, CHAINED_BLOCK], indent: 2
  ) { // stack: [PARENTHESIS_MULTILINE_CONTROL_BLOCK, CHAINED_BLOCK] → [BLOCK], indent: 0
  $user // stack: [BLOCK], indent: 1
    ?->getProfile() // stack: [BLOCK] → [BLOCK, CHAINED_BLOCK], indent: 2
    ?->update(['lastLogin' => time()]); // stack: [BLOCK, CHAINED_BLOCK], indent: 2
} // stack: [BLOCK] → [], indent: 0

// Null-safe dans une assignation multilignes
$address = $user?->getProfile() // stack: [] → [CHAINED_BLOCK], indent: 0
  ?->getContact() // stack: [CHAINED_BLOCK], indent: 1
  ?->getAddress() // stack: [CHAINED_BLOCK], indent: 1
  ?? 'N/A'; // stack: [CHAINED_BLOCK] → [], indent: 1