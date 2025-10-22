<?php

/**
 * Bad examples of method naming conventions.
 * Methods in classes and traits should be camelCase, but these are not.
 */
class BadMethodNamingExample {
  
  public function __construct() {
    // Constructor
  }

  public static function get_instance(): self {
    return new self();
  }

  /**
   * Improperly named method in snake_case.
   */
  public function my_method(): void {
    // Method content
  }

  public function ParseXml(): void {
    // Method content
  }

  public function convertXmlFile(): void {
    // Method content
  }

  public function callOtherMethods(): void {
    $this->my_method();
    $test = self::get_instance();
    $test->ParseXml();
  }



  protected function handle_event(): void {
    // Method content
  }

  private function parseData(): void {
    // Method content
  }// Static methods - incorrectly in snake_case
  // PascalCase methods - also invalid (should be camelCase)


  // ALL_CAPS method - invalid
  private function GET_DATA(): void {
    // Method content
  }
  

  /**
   * Magic methods - excluded from camelCase rules, so these are valid
   * @return string 
   */
  public function __toString(): string {
    return 'BadMethodNamingExample';
  }

}

// Trait with improperly named methods
trait BadMethodNamingTrait {

  public function trait_method(): void {
    // Method content
  }

  protected function do_complex_operation(): void {
    $this->trait_method();
  }

}

// Global functions are ignored by this sniff (handled by FunctionNameSniff)
function global_snake_case_function(): void {
  // Function content - this is actually valid for global functions
}

// Anonymous class with improper method naming
$anonymous = new class () {

  public function invalid_method(): void {
    // Method content
  }

  private function internal_helper(): void {
    // Method content
  }

};
