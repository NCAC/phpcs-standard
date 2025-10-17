<?php

namespace NCAC\Tests\Utils;

use NCAC\Utils\StringCaseHelper;
use PHPUnit\Framework\TestCase;

class StringCaseHelperTest extends TestCase {
  
  private StringCaseHelper $helper;

  // --- isCamelCase ---
  public function testIsCamelCase(): void {
    $this->assertTrue($this->helper->isCamelCase('fooBar'));
    $this->assertTrue($this->helper->isCamelCase('foo'));
    $this->assertFalse($this->helper->isCamelCase('FooBar'));
    $this->assertFalse($this->helper->isCamelCase('foo_bar'));
    $this->assertFalse($this->helper->isCamelCase('FOOBAR'));
  }

  // --- toCamelCase ---
  public function testToCamelCase(): void {
    $this->assertSame('myVariable', $this->helper->toCamelCase('my_variable'));
    $this->assertSame('myVariable', $this->helper->toCamelCase('myVariable'));
    $this->assertSame('myVariable', $this->helper->toCamelCase('myVariable'));
    $this->assertSame('isHtml', $this->helper->toCamelCase('IsHTML'));
  }

  // --- toSnakeCase ---
  public function testToSnakeCase(): void {
    $this->assertSame('my_variable', $this->helper->toSnakeCase('my_variable'));
    $this->assertSame('my_variable', $this->helper->toSnakeCase('myVariable'));
    $this->assertSame('is_html', $this->helper->toSnakeCase('IsHTML'));
  }

  // --- toPascalCase ---
  public function testToPascalCase(): void {
    $this->assertSame('MyVariable', $this->helper->toPascalCase('my_variable'));
    $this->assertSame('MyVariable', $this->helper->toPascalCase('myVariable'));
    $this->assertSame('IsHtml', $this->helper->toPascalCase('is_html'));
  }

  // --- toSnakeUpperCase ---
  public function testToSnakeUpperCase(): void {
    $this->assertSame('MY_VARIABLE', $this->helper->toSnakeUpperCase('my_variable'));
    $this->assertSame('MY_VARIABLE', $this->helper->toSnakeUpperCase('myVariable'));
    $this->assertSame('IS_HTML', $this->helper->toSnakeUpperCase('is_html'));
  }

  // --- isPascalCase ---
  public function testIsPascalCase(): void {
    $this->assertTrue($this->helper->isPascalCase('FooBar'));
    $this->assertFalse($this->helper->isPascalCase('fooBar'));
    $this->assertFalse($this->helper->isPascalCase('FOOBAR'));
    $this->assertFalse($this->helper->isPascalCase('FooBAR'));
  }

  // --- isSnakeCase ---
  public function testIsSnakeCase(): void {
    $this->assertTrue($this->helper->isSnakeCase('my_variable'));
    $this->assertTrue($this->helper->isSnakeCase('my'));
    $this->assertFalse($this->helper->isSnakeCase('Ma_Variable'));
    $this->assertFalse($this->helper->isSnakeCase('MA_Variable'));
    $this->assertFalse($this->helper->isSnakeCase('MA_VARIABLE'));
    $this->assertFalse($this->helper->isSnakeCase('myVariable'));
    $this->assertFalse($this->helper->isSnakeCase('my__variable'));
    $this->assertFalse($this->helper->isSnakeCase('_my_variable'));
    $this->assertFalse($this->helper->isSnakeCase('my_variable_'));
  }

  // --- isSnakeUpperCase ---
  public function testIsSnakeUpperCase(): void {
    $this->assertTrue($this->helper->isSnakeUpperCase('MY_VARIABLE'));
    $this->assertTrue($this->helper->isSnakeUpperCase('MY'));
    $this->assertFalse($this->helper->isSnakeUpperCase('my_variable'));
    $this->assertFalse($this->helper->isSnakeUpperCase('my_variable'));
    $this->assertFalse($this->helper->isSnakeUpperCase('my_variable'));
  }

  protected function setUp(): void {
    $this->helper = StringCaseHelper::me();
  }

}
