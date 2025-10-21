<?php

use Psalm\Progress\VoidProgress;

/**
 * Bad examples of method naming conventions.
 * Methods in classes and traits should be camelCase, but these are not.
 */

class BadMethodNamingExample {

  // Invalid snake_case method names
  public function my_method(): void {
    // Method content
  }

  public function calculate_total(): int {
    return 0;
  }

  private function parse_data(): void {
    // Method content
  }

  protected function handle_special_case(): void {
    // Method content
  }

  // Static methods - incorrectly in snake_case
  public static function get_instance(): self {
    return new self();
  }

  // PascalCase methods - also invalid (should be camelCase)
  public function ParseXml(): void {
    // Method content
  }

  protected function HandleEvent(): void {
    // Method content
  }

  // ALL_CAPS method - invalid
  private function GET_DATA(): void {
    // Method content
  }

  // Mixed case method - invalid
  public function convertXML_file(): void {
    // Method content
  }

  // Magic methods - excluded from camelCase rules, so these are valid
  public function __construct(): void {
    // Constructor
  }

  public function __toString(): string {
    return 'BadMethodNamingExample';
  }

  // Method calls to other methods - also incorrectly named in calls
  public function call_other_methods(): void {
    $this->my_method();
    $this->calculate_total();
    self::get_instance();
    static::handle_special_case();
    $this->ParseXml();
  }

}

// Trait with improperly named methods
trait BadMethodNamingTrait {

  public function trait_method(): void {
    // Method content
  }

  private function helper_function(): void {
    // Method content
  }

  // Protected method with PascalCase (invalid)
  protected function DoComplexOperation(): void {
    $this->trait_method();
    $this->helper_function();
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
