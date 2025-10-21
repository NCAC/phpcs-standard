<?php
/**
 * Good examples of method naming conventions.
 * Methods in classes and traits should be camelCase.
 */

class GoodMethodNamingExample {

  // Valid camelCase method names
  public function myMethod() {
    // Method content
  }
  
  public function calculateTotal() {
    return 0;
  }
  
  private function parseData() {
    // Method content
  }
  
  protected function handleSpecialCase() {
    // Method content
  }
  
  // Static methods - also camelCase
  public static function getInstance() {
    return new self();
  }
  
  // Magic methods - excluded from camelCase rules
  public function __construct() {
    // Constructor
  }
  
  public function __toString() {
    return 'GoodMethodNamingExample';
  }
  
  public function __call($name, $arguments) {
    // Magic method handler
  }
  
  // Method with parameters - still camelCase
  public function processItems($items, $options = []) {
    // Method content
  }
  
  // Method calls to other methods should be camelCase
  public function callOtherMethods() {
    $this->myMethod();
    $this->calculateTotal();
    self::getInstance();
    static::handleSpecialCase();
  }

}

// Trait with properly named methods
trait GoodMethodNamingTrait {

  public function traitMethod() {
    // Method content
  }
  
  private function helperFunction() {
    // Method content
  }
  
  // Protected method that calls other methods
  protected function doComplexOperation() {
    $this->traitMethod();
    $this->helperFunction();
  }

}

// Global functions are ignored by this sniff (handled by FunctionNameSniff)
function global_snake_case_function() {
  // Function content
}

// Anonymous class with proper method naming
$anonymous = new class {
  public function validMethod() {
    // Method content
  }
  
  private function internalHelper() {
    // Method content
  }
};
