<?php

// Invalid class names - should be PascalCase

// Line 5: snake_case class name
class my_class_name {

  public function testMethod() {
    return true;
  }
}

// Line 14: camelCase class name (should start with uppercase)
class camelCaseClassName {

  public function anotherMethod() {
    return false;
  }

}

// Line 25: all lowercase class name
class lowercase {

  public $property = 'test';

}

// Line 32: mixed case with underscore
class Mixed_case_Name {

  private $data;

}

// Line 39: interface with snake_case
interface user_interface {

  public function process();

}

// Line 46: trait with snake_case
trait helper_trait {

  public function help() {
    return 'helping';
  }

}

// Line 54: class with multiple underscores
class very_long_class_name_here {

  const CONSTANT = 'value';

}
