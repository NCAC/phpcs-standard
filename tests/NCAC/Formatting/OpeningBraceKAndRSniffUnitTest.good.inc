<?php

// GOOD: Class opening brace on same line (K&R style)
class Test {

  /**
   * @var string
   */
  private $var = 'Test';

  /**
   * This is a test function.
   * @return void
   */
  public function testFunction(): void {
    if ($this->var === 'Test') {
      echo "This is a test.";
    } else {
      echo "This is not a test.";
    }
  }
}

// GOOD: Interface opening brace on same line
interface TestInterface {
  public function doSomething(): void;
}

// GOOD: Trait opening brace on same line
trait TestTrait {
  public function traitMethod(): void {
    echo "trait";
  }
}

// GOOD: Function with brace on same line
function good_function() {
  echo "good";
}

// GOOD: Anonymous class
$anonymous_class = new class {
  public function anonymousMethod(): void {
    echo "anonymous";
  }
};
