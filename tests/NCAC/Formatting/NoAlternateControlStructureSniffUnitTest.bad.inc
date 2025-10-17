<?php

if ($a): // error : alternative syntax
  echo "if"; // error (TwoSpacesIndentSniff)
elseif ($b):
  echo "elseif";
else:
  echo "else";
endif;

while ($i < 10):
  $i++;
endwhile;

for ($i = 0; $i < 10; $i++):
  echo $i;
endfor;

foreach ($arr as $v):
  echo $v;
endforeach;

switch ($x):
  case 1:
    echo "one";
    break;
  default:
    echo "default";
endswitch;
