<?php

/**
 * @return array<string, mixed>
 */
function test_array_in_case(string $path): array {
  
  switch ($path) {
    case 'ok':
      return [
        'is_ok' => true,
        'is_not_ok' => false
      ];
    case 'nok':
      return [
        'is_ok' => false,
        'is_not_ok' => true
      ];
    
    default:
      return [
        'is_ok' => null,
        'is_not_ok' => null
      ];
  }
}
