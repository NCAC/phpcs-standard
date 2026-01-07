<?php

// Invalid function parameters - should be snake_case
function test_function($CamelCaseParam, $PascalCaseParam) {
  return $CamelCaseParam + $PascalCaseParam;
}
$test_closure = function ($WrongParam) {
  return $WrongParam;
};

// Invalid class properties - should be camelCase
class TestClass {

  public $snake_case_property;

  private $PascalCaseProperty;

  protected $another_snake_case;

  public static $UPPER_CASE_STATIC;

  public $old_style_var;

}

// Invalid trait properties - should be camelCase
trait TestTrait {

  public $snake_case_trait;

  private $PascalCaseTrait;

}

// Invalid dynamic properties - should be snake_case
$obj = new stdClass();
$CamelCaseVar = 'test';
$PascalCaseVar = 'another';
$obj->$CamelCaseVar = 1;
$obj->$PascalCaseVar = 2;

// Invalid local variables - should be snake_case
function test_local_function() {
  $LocalVar = 'local';
  $CamelCaseLocal = 'test';
  $PascalCaseLocal = 'another';

  return $LocalVar . $CamelCaseLocal . $PascalCaseLocal;
}

// Invalid global variables - should be snake_case
$GlobalVar = 'global';
$CamelCaseGlobal = 'test';
$PascalCaseGlobal = 'another';

// Mixed invalid contexts
class MixedInvalidExample {
  public $snake_case_prop;
  private $PascalCaseProp;

  public function testMethod($CamelCaseParam, $PascalParam) {
    $LocalVariable = $CamelCaseParam;
    $CamelCaseLocal = $PascalParam;
    $DynamicVar = 'property';
    $this->$DynamicVar = $LocalVariable;

    return $this->snake_case_prop;
  }

}

// Multiple errors on same line (parameters)
function multiple_params($FirstParam, $SecondParam, $ThirdParam) {
  return $FirstParam + $SecondParam + $ThirdParam;
}

// Complex invalid case
class ComplexInvalidClass {

  public $Wrong_Property;

  public static $AnotherWrongProperty;

  public function complexMethod($WrongParam) {
    $WrongLocal = $WrongParam;
    $DynamicProp = 'test';
    $this->$DynamicProp = $WrongLocal;

    global $WrongGlobal;
    $WrongGlobal = 'global';

    // Access static property - should detect PascalCase as invalid
    $value = self::$AnotherWrongProperty;

    return $this->Wrong_Property;
  }

}
