<?php

// Multiple spaces between function keyword and name
function   my_function(): void {
  echo "test";
}

function  another_function(): string {
  return 'test';
}

class    TestClass {

  public function    methodWithSpaces(): void {
    echo "test";
  }

}

interface   TestInterface {

  public function doSomething(): void;

}

trait  TestTrait {

  public function traitMethod(): void {
    echo "trait";
  }

}

enum    Status {

  case PENDING;

}

abstract class  AbstractClass {

  abstract public function   abstractMethod(): void;

  public function concreteMethod(): string {
    return 'test';
  }

}

final class     FinalClass {

  final public function  finalMethod(): void {
    echo "final";
  }

}
