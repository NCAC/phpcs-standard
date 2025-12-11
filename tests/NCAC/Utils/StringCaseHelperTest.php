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

  // --- isSnakeCase with allowDoubleUnderscore ---
  public function testIsSnakeCaseWithAllowDoubleUnderscore(): void {
    // Without option: double underscores are rejected
    $this->assertFalse($this->helper->isSnakeCase('my__variable', false, false));
    $this->assertFalse($this->helper->isSnakeCase('preprocess_node__homepage', false, false));

    // With option: double underscores are allowed
    $this->assertTrue($this->helper->isSnakeCase('my__variable', true, false));
    $this->assertTrue($this->helper->isSnakeCase('preprocess_node__homepage', true, false));
    $this->assertTrue($this->helper->isSnakeCase('theme_suggestions_node__alter', true, false));
  }

  // --- isSnakeCase with allowLeadingUnderscore ---
  public function testIsSnakeCaseWithAllowLeadingUnderscore(): void {
    // Without option: leading underscores are rejected
    $this->assertFalse($this->helper->isSnakeCase('_my_variable', false, false));
    $this->assertFalse($this->helper->isSnakeCase('_internal_helper', false, false));

    // With option: leading underscores are allowed
    $this->assertTrue($this->helper->isSnakeCase('_my_variable', false, true));
    $this->assertTrue($this->helper->isSnakeCase('_internal_helper', false, true));
    $this->assertTrue($this->helper->isSnakeCase('_calculate_price', false, true));

    // Trailing underscores are never allowed
    $this->assertFalse($this->helper->isSnakeCase('my_variable_', false, true));
  }

  // --- isSnakeCase with both options ---
  public function testIsSnakeCaseWithBothOptions(): void {
    // With both options: both __ and leading _ are allowed
    $this->assertTrue($this->helper->isSnakeCase('_preprocess_node__homepage', true, true));
    $this->assertTrue($this->helper->isSnakeCase('_my__custom__function', true, true));
  }

  // --- toSnakeCase with allowDoubleUnderscore ---
  public function testToSnakeCaseWithAllowDoubleUnderscore(): void {
    // Without option: double underscores are collapsed to single
    $this->assertSame('my_variable', $this->helper->toSnakeCase('my__variable', false, false));
    $this->assertSame('my_variable', $this->helper->toSnakeCase('my___variable', false, false));

    // With option: double underscores are preserved
    $this->assertSame('my__variable', $this->helper->toSnakeCase('my__variable', true, false));
    $this->assertSame('preprocess_node__homepage', $this->helper->toSnakeCase('preprocessNode__homepage', true, false));

    // Triple+ underscores are normalized to double
    $this->assertSame('my__variable', $this->helper->toSnakeCase('my___variable', true, false));
    $this->assertSame('my__variable', $this->helper->toSnakeCase('my____variable', true, false));
  }

  // --- toSnakeCase with allowLeadingUnderscore ---
  public function testToSnakeCaseWithAllowLeadingUnderscore(): void {
    // Without option: leading underscores are removed
    $this->assertSame('my_variable', $this->helper->toSnakeCase('_my_variable', false, false));
    $this->assertSame('my_variable', $this->helper->toSnakeCase('_myVariable', false, false));

    // With option: leading underscores are preserved
    $this->assertSame('_my_variable', $this->helper->toSnakeCase('_my_variable', false, true));
    $this->assertSame('_my_variable', $this->helper->toSnakeCase('_myVariable', false, true));
    $this->assertSame('_calculate_price', $this->helper->toSnakeCase('_calculatePrice', false, true));
  }

  // --- toSnakeCase with both options ---
  public function testToSnakeCaseWithBothOptions(): void {
    // With both options: preserve both __ and leading _
    $this->assertSame('_preprocess_node__homepage', $this->helper->toSnakeCase('_preprocessNode__homepage', true, true));
    $this->assertSame('_my_custom__function', $this->helper->toSnakeCase('_myCustom__function', true, true));
    $this->assertSame('_my__variable', $this->helper->toSnakeCase('_my__variable', true, true));
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
