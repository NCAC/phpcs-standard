<?php

/**
 * Good examples of variable naming conventions.
 * @param mixed $snake_case_param 
 * @param mixed $another_param 
 */
function valid_function($snake_case_param, $another_param): mixed {
  return $snake_case_param + $another_param;
}

// Valid closure parameters
$valid_closure = function ($closure_param, $another_closure_param) {
  return $closure_param . $another_closure_param;
};

// Valid class with camelCase properties
class ValidClass {

  public static mixed $staticCamelCase;

  public mixed $camelCaseProperty;

  public mixed $oldStyleCamelCase;

  protected mixed $validProperty;

  private mixed $anotherCamelCase;

}

// Valid trait with camelCase properties
trait ValidTrait {

  public string $traitProperty;
  private mixed $anotherTraitProperty;

}

// Valid dynamic properties in snake_case
$obj = new stdClass();
$snake_case_var = 'test';
$another_snake_var = 'another';
$obj->$snake_case_var = 1;
$obj->$another_snake_var = 2;

// Valid local variables in snake_case
function test_local_vars(): string {
  // no errors should be reported here, as these are local variables
  static $static_var = 'static';
  $local_var = 'local';
  $snake_case_local = 'test';
  $another_local = 'another';

  return $local_var . $snake_case_local . $another_local . $static_var;
}

// Valid global variables in snake_case
$global_var = 'global';
$snake_case_global = 'test';
$another_global = 'another';

// Valid PHP superglobals (should be ignored)
$_GET['key'] = 'value';
$_POST['data'] = 'test';
$_SERVER['HTTP_HOST'] = 'localhost';
$_FILES['upload'] = [];
$_COOKIE['session'] = 'abc123';
$_SESSION['user'] = 'john';
$_ENV['PATH'] = '/usr/bin';
$_REQUEST['param'] = 'value';
$GLOBALS['config'] = [];

// Valid mixed contexts
class MixedValidExample {

  public mixed $validProperty;

  public function validMethod(int $valid_param): mixed {
    $local_variable = $valid_param;
    $dynamic_var = 'property';
    $this->$dynamic_var = $local_variable;

    return $this->validProperty;
  }

}
