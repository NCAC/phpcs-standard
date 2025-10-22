<?php
/**
 * Good examples of method naming conventions.
 * Methods in classes and traits should be camelCase.
 */

class GoodMethodNamingExample {

  public function __construct() {
    // Constructor
  }

  public static function getInstance(): self {
    return new self();
  }

  /**
   * A properly named method in camelCase.
   */
  public function myMethod(): void {
    // Method content
  }
  
  /**
   * Another properly named method.
   */
  public function calculateTotal(): int {
    return 0;
  }
  
  /**
   * Calls other methods within the class.
   */
  public function callOtherMethods(): void {
    $this->myMethod();
    $this->calculateTotal();
    self::getInstance();
    static::handleSpecialCase();
  }

  protected function handleSpecialCase(): void {
    // Method content
  }

  private function parseData(): void {
    // Method content
  }
  
  public function __toString(): string {
    return 'GoodMethodNamingExample';
  }
  
  /**
   * Magic method with proper naming.
   * @param array<string, mixed> $arguments 
   */
  public function __call(string $name, array $arguments): void {
    // Magic method handler
  }

}

// Trait with properly named methods
trait GoodMethodNamingTrait {

  public function traitMethod(): void {
    // Method content
  }
  
  protected function doComplexOperation(): void {
    $this->traitMethod();
    $this->helperFunction();
  }

  private function helperFunction(): void {
  }

}

// Global functions are ignored by this sniff (handled by FunctionNameSniff)
function global_snake_case_function(): void {
  // Function content
  
}

// Anonymous class with proper method naming
$anonymous = new class {
  public function validMethod(): void {
    // Method content
  }
  
  private function internalHelper(): void {
    // Method content
  }
};
