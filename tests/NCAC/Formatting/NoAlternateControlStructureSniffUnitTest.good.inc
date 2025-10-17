<?php

declare(strict_types=1);


$var_1 = 1;
$var_2 = 2;

if ($var_1 === 1) {
  // Some code here
  $var_2 = 3;
} else {
  // Some other code here
  $var_2 = 4;
}

for ($i = 0; $i < 10; $i++) {
  // Looping code here
  $var_1 += $i;
}

$array = [1, 2, 3, 4, 5];
foreach ($array as $value) {
  $var_2 += $value;
}



while ($var_2 < 10) {
  // Looping code here
  $var_1++;
}

switch ($var_1) {
  case 1:
    $var_2 = true;
    break;
  case 2:
    $var_2 = false;
    break;
  default:
    $var_2 = null;
    break;
}
