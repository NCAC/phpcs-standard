<?php

class GoodClosureIndentation {

  /**
   * 
   * @return Closure(mixed $x): int|float 
   */
  public function simpleClosure() {
    $closure = function($x) {
      return $x * 2;
    };
    return $closure;
  }

  /**
   * @param array<int, mixed> $items
   * @return array<string|int, mixed> 
   */
  public function closureWithArguments(array $items): array {
    $result = array_map(function($item) {
      return $item->getValue();
    }, $items);
    return $result;
  }

  /**
   * 
   * @return Closure(mixed $x): mixed 
   */
  public function nestedClosure(): Closure {
    $outer = function($x) {
      $inner = function($y) {
        return $y + 1;
      };
      return $inner($x);
    };
    return $outer;
  }

  /**
   * 
   * @return Closure(mixed $data): mixed 
   */
  public function closureInIfBlock(): Closure {
    if (true) {
      $callback = function($data) {
        return $data->process();
      };
      return $callback;
    }
  }

  /**
   * 
   * @return Closure(mixed $param1, mixed $param2): mixed 
   */
  public function multilineClosure(): Closure {
    $complex = function($param1, $param2) {
      $result = $param1 + $param2;
      if ($result > 10) {
        return $result * 2;
      }
      return $result;
    };
    return $complex;
  }

  /**
   * 
   * @return Closure(mixed $x): int|float 
   */
  public function closureWithUse() {
    $multiplier = 3;
    $closure = function($x) use ($multiplier) {
      return $x * $multiplier;
    };
    return $closure;
  }

  /**
   * 
   * @return array<string|int, mixed> 
   */
  public function arrayWithClosures(): array {
    $callbacks = [
      'add' => function($a, $b) {
        return $a + $b;
      },
      'multiply' => function($a, $b) {
        return $a * $b;
      }
    ];
    return $callbacks;
  }

}
