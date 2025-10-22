<?php

namespace NCAC\Tests\NamingConventions;

use NCAC\Tests\SniffUnitTest;

/**
 * Unit tests for NCAC\Sniffs\NamingConventions\VariableNameSniff.
 *
 * Tests comprehensive variable naming conventions across different contexts:
 * - Function/closure parameters: snake_case
 * - Class/trait properties: camelCase
 * - Dynamic properties: snake_case
 * - Local variables: snake_case
 * - Global variables: snake_case
 * - PHP superglobals: excluded from rules
 *
 * @category CodingStandard
 * @package  NCAC\Tests\NamingConventions
 * @author   NCAC
 * @license  MIT
 * @link     https://github.com/ncac-php/standard
 */
class VariableNameSniffUnitTest extends SniffUnitTest {

  /**
   * Returns the lines where errors should occur.
   *
   * The key of the array should represent the line number and the value
   * should represent the number of errors that should occur on that line.
   *
   * @return array<int, int> Lines with expected error counts.
   */
  public function getErrorList(string $test_file): array {
    switch ($test_file) {
      case 'VariableNameSniffUnitTest.bad.inc':
        return [
          // Function parameters - should be snake_case
          4 => 1,   // function test_function($CamelCaseParam, $PascalCaseParam)
          5 => 1,   // return $CamelCaseParam + $PascalCaseParam;
          7 => 1,   // $test_closure = function ($WrongParam) {
          8 => 1,   // variabl $wrongPreturn $WrongParam;
                  
          // Class properties - should be camelCase  
          14 => 1,  // public $snake_case_property;
          16 => 1,  // private $PascalCaseProperty;
          18 => 1,  // protected $another_snake_case;
          20 => 1,  // static $UPPER_CASE_STATIC;
          22 => 1,  // public $old_style_var;
                  
          // trait properties - should be camelCase
          29 => 1,  // public $snake_case_trait;
          31 => 1,  // private $PascalCaseTrait;

          // Dynamic properties - should be snake_case
          37 => 1,  // $CamelCaseVar = 'test';
          38 => 1,  // $PascalCaseVar = 'another';
          39 => 1,  // $obj->$CamelCaseVar = 1;
          40 => 1,  // $obj->$PascalCaseVar = 2;
                  
          // Local variables - should be snake_case
          44 => 1,  // $LocalVar = 'local';
          45 => 1,  // $CamelCaseLocal = 'test';
          46 => 1,  // $PascalCaseLocal = 'another';
          48 => 1,  // return $LocalVar . $CamelCaseLocal . $PascalCaseLocal;
                  
          // Global variables - should be snake_case  
          52 => 1,  // $GlobalVar = 'global';
          53 => 1,  // $CamelCaseGlobal = 'test';
          54 => 1,  // $PascalCaseGlobal = 'another';

          // // Mixed invalid contexts
          58 => 1,  // public $snake_case_prop;
          59 => 1,  // private $PascalCaseProp;

          // Méthode avec paramètres et variables invalides
          61 => 1,  // public function testMethod($CamelCaseParam, $PascalParam) {
          62 => 1,  // $LocalVariable = $CamelCaseParam;
          63 => 1,  // $CamelCaseLocal = $PascalParam;
          64 => 1,  // $DynamicVar = 'property';
          65 => 1,  // $this->$DynamicVar = $LocalVariable;

          // Multiple errors on same line (parameters)
          73 => 1,  // function multiple_params($FirstParam, $SecondParam, $ThirdParam) {
          74 => 1,  // return $FirstParam + $SecondParam + $ThirdParam;
                  
          // Complex invalid case
          80 => 1,  // public $Wrong_Property;
          82 => 1,  // public static $AnotherWrongProperty;
          84 => 1,  // public function complexMethod($WrongParam) {
          85 => 1,  // $WrongLocal = $WrongParam;
          86 => 1,  // $DynamicProp = 'test';
          87 => 1,  // $this->$DynamicProp = $WrongLocal;
          89 => 1,  // global $WrongGlobal;
          90 => 1   // $WrongGlobal = 'global';
        ];

      case 'VariableNameSniffUnitTest.good.inc':
        return [];

      default:
        return [];
        break;
    }
  }

  /**
   * Returns the lines where warnings should occur.
   *
   * @return array<int, int> Lines with expected warning counts.
   */
  public function getWarningList(string $test_file): array {
    return [];
  }

  /**
   * Returns the path to the ruleset XML file for this test.
   */
  protected function getStandard(): string {
    return __DIR__ . '/ruleset.namingConventions.variableName.xml';
  }

}
