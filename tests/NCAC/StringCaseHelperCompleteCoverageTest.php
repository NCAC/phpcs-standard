<?php

namespace NCAC\Tests\NCAC;

use NCAC\Utils\StringCaseHelper;
use PHPUnit\Framework\TestCase;

/**
 * Complete tests to cover all StringCaseHelper branches.
 */
class StringCaseHelperCompleteCoverageTest extends TestCase {

  private StringCaseHelper $helper;

  protected function setUp(): void {
    $this->helper = new StringCaseHelper();
  }

  /**
   * Test to cover all branches of repeatsUppercaseChars()
   */
  public function testRepeatsUppercaseCharsEdgeCases(): void {
    // Test different cases to maximize coverage
    $this->assertTrue($this->helper->isPascalCase('SimpleTest')); // normal case
    $this->assertFalse($this->helper->isPascalCase('XMLParser')); // double uppercase
    $this->assertFalse($this->helper->isPascalCase('APIManager')); // triple uppercase
    $this->assertTrue($this->helper->isPascalCase('XmlParser')); // no double uppercase
  }

  /**
   * Test to cover all branches of hasUpperChars()
   */
  public function testHasUpperCharsEdgeCases(): void {
    // Tests for hasUpperChars via isCamelCase
    $this->assertTrue($this->helper->isCamelCase('simpleTest')); // has uppercase letters
    $this->assertTrue($this->helper->isCamelCase('simple')); // all lowercase is valid camelCase
    $this->assertFalse($this->helper->isCamelCase('testAPI')); // ends with uppercase (invalid)
  }

  /**
   * Test to cover toSnakeCase() branches
   */
  public function testToSnakeCaseAllBranches(): void {
    // Test different transformations
    $this->assertEquals('simple', $this->helper->toSnakeCase('simple'));
    $this->assertEquals('simple_test', $this->helper->toSnakeCase('SimpleTest'));
    $this->assertEquals('camel_case', $this->helper->toSnakeCase('camelCase'));
    $this->assertEquals('xmlparser', $this->helper->toSnakeCase('XMLParser'));
    $this->assertEquals('abc', $this->helper->toSnakeCase('ABC'));
    $this->assertEquals('test123', $this->helper->toSnakeCase('test123'));
    $this->assertEquals('test123_abc', $this->helper->toSnakeCase('Test123ABC'));
  }

  /**
   * Test to cover complex branches of isSnakeCase()
   */
  public function testIsSnakeCaseComplexCases(): void {
    // Cases with allowDoubleUnderscore
    $this->assertTrue($this->helper->isSnakeCase('simple_case', false));
    $this->assertTrue($this->helper->isSnakeCase('simple_case', true));
    $this->assertFalse($this->helper->isSnakeCase('simple__case', false));
    $this->assertTrue($this->helper->isSnakeCase('simple__case', true));

    // Edge cases
    $this->assertFalse($this->helper->isSnakeCase(''));
    $this->assertTrue($this->helper->isSnakeCase('a'));
    $this->assertFalse($this->helper->isSnakeCase('Simple'));
    $this->assertFalse($this->helper->isSnakeCase('simple-case'));
    $this->assertTrue($this->helper->isSnakeCase('simple_case_123'));
  }

  /**
   * Test to maximize coverage on edge cases
   */
  public function testEdgeCasesMaxCoverage(): void {
    // Tests with very short strings
    $this->assertFalse($this->helper->isPascalCase(''));
    $this->assertFalse($this->helper->isPascalCase('a'));
    $this->assertFalse($this->helper->isPascalCase('A'));

    // Tests with strings containing numbers
    $this->assertTrue($this->helper->isPascalCase('Test2'));
    $this->assertFalse($this->helper->isCamelCase('test2'));
    $this->assertTrue($this->helper->isCamelCase('test2Code'));

    // Tests snake_case with numbers and underscores
    $this->assertTrue($this->helper->isSnakeCase('test_123'));
    $this->assertTrue($this->helper->isSnakeCase('test_123_abc'));
    $this->assertFalse($this->helper->isSnakeCase('test_123_ABC'));
  }
}
