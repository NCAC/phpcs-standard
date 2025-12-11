<?php

class GoodClassSpacing {

  /**
   * This is a sample method.
   */
  public function methodOne(): void {
    // Some code here
  }

  /**
   * This is another sample method.
   */
  public function methodTwo(): void {
    // Some code here
  }
}

// Test case: class with implements
interface TestInterface {

  public function test(): void;
}

class GoodClassWithImplements implements TestInterface {

  public function test(): void {
    // Implementation
  }
}

// Test case: class with implements and comment after brace (regression test for bug)
class GoodClassWithImplementsAndComment implements TestInterface {

  // This comment should not affect blank line detection
  public function test(): void {
    // Implementation
  }
}

// Test case: class with extends
class BaseClass {

  protected $value = 'base';
}

class GoodClassWithExtends extends BaseClass {

  protected $value = 'extended';
}

// Test case: class with both extends and implements
class GoodClassWithExtendsAndImplements extends BaseClass implements TestInterface {

  public function test(): void {
    // Implementation
  }
}

// Test case: class with multiple interfaces
interface AnotherInterface {

  public function another(): void;
}

class GoodClassWithMultipleInterfaces implements TestInterface, AnotherInterface {

  public function test(): void {
    // Implementation
  }

  public function another(): void {
    // Implementation
  }
}
