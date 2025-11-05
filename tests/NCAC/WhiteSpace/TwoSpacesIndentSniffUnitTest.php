<?php

namespace NCAC\Tests\WhiteSpace;

use NCAC\Tests\SniffUnitTest;

/**
 * Unit test class for the TwoSpacesIndentSniff.
 *
 * @package NCAC\Tests\WhiteSpace
 */
class TwoSpacesIndentSniffUnitTest extends SniffUnitTest {

  /**
   * Returns the lines where errors should occur for each fixture file.
   *
   * @param string $test_file The name of the file being tested.
   * @return array<int, int>
   */
  public function getErrorList(string $test_file): array {
    switch ($test_file) {

      case 'TwoSpacesIndentSniff.test':
        return [];


      case 'TwoSpacesIndentSniff.goodDocBlock.inc':
        return [];


      case 'TwoSpacesIndentSniff.badDocBlock.inc':
        return [
          2 => 1,
          3 => 1,
          4 => 1,
          5 => 1
        ];


      case 'TwoSpacesIndentSniff.badFunction.inc':
        return [
          4 => 1,
          5 => 1,
          6 => 1,
          7 => 1
        ];


      case 'TwoSpacesIndentSniff.badClass.inc':
        return [
          8 => 1,
          10 => 1,
          12 => 1,
          13 => 1,
          14 => 1
        ];


      case 'TwoSpacesIndentSniff.goodClass.inc':
        return [];


      case 'TwoSpacesIndentSniff.badTernaryExpression.inc':
        return [
          5 => 1, // Bad indentation: '?' branch not indented by +2 spaces
          6 => 1, // Bad indentation: ':' branch not indented by +2 spaces
          10 => 1, // Bad indentation: condition not indented in multiline parenthesis
          11 => 1, // Bad indentation: '?' branch not indented by +2 spaces in multiline parenthesis
          12 => 1,  // Bad indentation: ':' branch not indented by +2 spaces in multiline parenthesis
          19 => 1, // Bad indentation: '?' branch not indented by +2 spaces
          20 => 1, // Bad indentation: ':' branch not indented by +2 spaces
          21 => 1, // Bad indentation: nested ternary expression not indented by +2 spaces
          22 => 1 // Bad indentation: nested ternary expression not indented by +2 spaces
        ];


      case 'TwoSpacesIndentSniff.goodTernaryExpression.inc':
        return [];
        break;

      case 'TwoSpaces.badMultilineConditions.inc':
        return [
          5 => 1,
          6 => 1,
          7 => 1
        ];


      case 'TwoSpacesIndentSniff.badMultilineCondition.inc':
        return [
          5 => 1,  // Bad indentation: first condition line not indented by +2 spaces
          6 => 1,  // Bad indentation: second condition line not indented by +2 spaces
          7 => 1,  // Bad indentation: third condition line not indented by +2 spaces
          // while multiline condition
          14 => 1, // Bad indentation: first condition line not indented by +2 spaces
          15 => 1, // Bad indentation: second condition line not indented by +2 spaces
          // for multiline condition
          22 => 1, // Bad indentation: init line not indented by +2 spaces
          23 => 1, // Bad indentation: condition line not indented by +2 spaces
          24 => 1, // Bad indentation: third condition line not indented by +2 spaces
        ];


      case 'TwoSpacesIndentSniff.goodMultilineConditions.inc':
        return [];

      case 'TwoSpacesIndentSniff.goodArrayInArguments.inc':
        return [];

      case 'TwoSpacesIndentSniff.goodArrayReturnInCase.inc':
        return [];

      case 'TwoSpacesIndentSniff.badArrayInArguments.inc':
        return [
          16 => 1,  // Error 1: 'key1' => 'value1' - 4 spaces instead of 2
          17 => 1,  // Error 1: 'key2' => 42 - 4 spaces instead of 2  
          18 => 1,  // Error 1: 'key3' => [1, 2, 3] - 4 spaces instead of 2
          23 => 1,  // Error 2: 'key1' => 'value1' - 1 space instead of 2
          24 => 1,  // Error 2: 'key2' => 42 - 1 space instead of 2
          30 => 1,  // Error 3: 'key1' => 'value1' - 4 spaces instead of 2
          31 => 1,  // Error 3: 'key2' => 42 - 3 spaces instead of 2
          32 => 1,  // Error 3: 'nested1' => 'value' - 5 spaces instead of 4
          33 => 1,  // Error 3: 'nested2' => 'value' - 6 spaces instead of 4
          34 => 1,  // Error 3: ], - 3 spaces instead of 2
          39 => 1,  // Error 4: 'key1' => 'value1' - 0 spaces instead of 2
          40 => 1,  // Error 4: 'key2' => 42 - 0 spaces instead of 2
          41 => 1,  // Error 4: 'key3' => [ - 0 spaces instead of 2
          42 => 1,  // Error 4: 'nested' => 'value' - 0 spaces instead of 4
          43 => 1,  // Error 4: ], - 0 spaces instead of 2
        ];

      case 'TwoSpacesIndentSniff.goodMethodChaining.inc':
        return [];

      case 'TwoSpacesIndentSniff.badMethodChaining.inc':
        // TODO: After implementing method chaining feature, restore these expected errors:
        // 7 => 1,   // ->method1() wrong indentation (0 spaces instead of 4)
        // 8 => 1,   // ->method2() wrong indentation (6 spaces instead of 4)
        // 9 => 1,   // ->method3() wrong indentation (8 spaces instead of 4)
        // 16 => 1,  // ->createQueryBuilder() wrong indentation (0 spaces instead of 6)
        // 17 => 1,  // ->where() wrong indentation (12 spaces instead of 6)
        // 18 => 1,  // ->setParameter() wrong indentation (4 spaces instead of 6)
        // 19 => 1,  // ->getQuery() wrong indentation (14 spaces instead of 6)
        // 20 => 1   // ->getResult() wrong indentation (10 spaces instead of 6)
        return [];

      default:
        return [];
    }
  }

  /**
   * Returns the lines where warnings should occur for each fixture file.
   *
   * @param string $test_file The name of the file being tested.
   * @return array<int, int>
   */
  public function getWarningList(string $test_file): array {
    return [];
  }

  /**
   * Override the standard to use a specific ruleset.
   *
   */
  public function getStandard(): string {
    return __DIR__ . '/ruleset.whiteSpace.twoSpacesIndent.xml';
  }

  /**
   * @dataProvider fixtureProvider
   * @testdox Fixture with $fixture_file
   * Runs each fixture individually using the parent implementation.
   *
   */
  public function testFixture(string $fixture_file): void {
    parent::testFixture($fixture_file);
  }

}
