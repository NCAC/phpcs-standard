<?php

// Correct spacing in function declarations
function my_function(): void {
  echo "test";
}

function another_function(): string {
  return 'test';
}

// Correct spacing in class declaration
class TestClass {

  public function methodName(): void {
    echo "test";
  }

  private function anotherMethod(): string {
    return 'value';
  }

}

// Correct spacing in interface declaration
interface TestInterface {

  public function do_something(): void;

  public function get_value(): string;

}

// Correct spacing in trait declaration
trait TestTrait {

  public function traitMethod(): void {
    echo "trait";
  }

}

// Correct spacing in enum declaration
enum Status {

  case PENDING;
  case APPROVED;
  case REJECTED;

}

// Correct spacing with abstract
abstract class AbstractClass {

  abstract public function abstractMethod(): void;

  public function concreteMethod(): string {
    return 'test';
  }

}

// Correct spacing with final
final class FinalClass {

  final public function finalMethod(): void {
    echo "final";
  }

}

// Correct spacing with readonly (PHP 8.2+)
readonly class ReadonlyClass {

  public function __construct(
    public string $name,
  ) {
  }

}
