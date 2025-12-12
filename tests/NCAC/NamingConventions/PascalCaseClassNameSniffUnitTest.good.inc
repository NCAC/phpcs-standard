<?php

// Valid class names - proper PascalCase

class MyClassName {

  public function testMethod() {
    return true;
  }

}

class CamelCaseClassName {

  public function anotherMethod() {
    return false;
  }

}

class Lowercase {

  public $property = 'test';

}

class MixedCaseName {

  private $data;

}

interface UserInterface {

  public function process();

}

trait HelperTrait {

  public function help() {
    return 'helping';
  }

}

class VeryLongClassNameHere {

  const CONSTANT = 'value';

}
