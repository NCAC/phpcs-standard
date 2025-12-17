<?php

class BadClosureIndentation {

  public function closureInReturnStatement(): string {
    return join(' - ', array_map(function($x) {
          return $x * 2;  // Should be 2 spaces, found 6
    }, [1, 2, 3]));
  }

  /**
   * @param array<int, mixed> $items
   * @return array<string|int, mixed> 
   */
  public function closureInVariableAssignment(array $items): array {
    $result = array_map(function($item) {
        return $item->getValue();  // Should be 2 spaces, found 4
    }, $items);
    return $result;
  }

  /**
   * 
   * @return Closure(mixed $x): mixed 
   */
  public function wrongNestedIndentation(): Closure {
    $outer = function($x) {
      $inner = function($y) {  // Should be 2 spaces, found 4
        return $y + 1;  // Should be 4 spaces, found 6
      };
      return $inner($x);  // Should be 2 spaces, found 2 (correct)
    };
    return $outer;
  }

  /**
   * 
   * @return Closure(mixed $data): mixed|void 
   */
  public function wrongIndentationInIfBlock() {
    if (true) {
      $callback = function($data) {
        return $data->process();  // Should be 4 spaces, found 6
      };
      return $callback;
    }
  }

  /**
   * 
   * @return Closure(mixed $param1, mixed $param2): mixed 
   */
  public function wrongMultilineIndentation(): Closure {
    $complex = function($param1, $param2) {
      $result = $param1 + $param2;  // Should be 2 spaces, found 4
      if ($result > 10) {  // Should be 2 spaces, found 2 (correct)
        return $result * 2;  // Should be 4 spaces, found 6
      }
      return $result;  // Should be 2 spaces, found 4
    };
    return $complex;
  }

  /**
   * 
   * @return Closure(mixed $x): int|float 
   */
  public function wrongIndentationWithUse() {
    $multiplier = 3;
    $closure = function($x) use ($multiplier) {
      return $x * $multiplier;  // Should be 2 spaces, found 4
    };
    return $closure;
  }

  /**
   * 
   * @return array{add: \Closure(mixed $a, mixed $b): mixed, multiply: \Closure(mixed $a, mixed $b): int|float} 
   */
  public function wrongArrayIndentation(): array {
    $callbacks = [
      'add' => function($a, $b) {
        return $a + $b;  // Should be 4 spaces, found 6
      },
      'multiply' => function($a, $b) {
        return $a * $b;  // Should be 4 spaces, found 4 (correct)
      }
    ];
    return $callbacks;
  }

}
