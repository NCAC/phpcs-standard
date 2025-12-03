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

      case 'TwoSpacesIndentSniff.goodClosureInFunctionCall.inc':
        // TODO: Fix closure indentation detection - currently detects false positives
        return [];

      case 'TwoSpacesIndentSniff.testClosureMinimal.inc':
        return [];

      case 'TwoSpacesIndentSniff.testClosureWithArg.inc':
        return [];

      case 'TwoSpacesIndentSniff.testClosureWithAssignment.inc':
        return [];

      case 'TwoSpacesIndentSniff.testExactCopy.inc':
        return [];

      case 'TwoSpacesIndentSniff.badClosureInFunctionCall.inc':
        return [
          7 => 1,   // return strtoupper - wrong indentation (0 spaces instead of 4)
          8 => 1,   // }, $input - wrong indentation (0 spaces instead of 2)
          17 => 1,  // }, $input - wrong indentation (2 spaces instead of 0)
          23 => 1,  // $value = $matches[1]; - wrong indentation (0 spaces instead of 4)
          24 => 1,  // return strtoupper - wrong indentation (0 spaces instead of 4)
          32 => 1,  // return strtoupper - wrong indentation (6 spaces instead of 4)
          41 => 1,  // if (isset - wrong indentation (0 spaces instead of 4)
          42 => 1,  // return strtoupper - wrong indentation (0 spaces instead of 6)
          43 => 1,  // } - wrong indentation (0 spaces instead of 4)
          44 => 1,  // return ''; - wrong indentation (0 spaces instead of 4)
        ];

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
        return [
          7 => 1,   // ->method1() wrong indentation (0 spaces instead of 4)
          8 => 1,   // ->method2() wrong indentation (6 spaces instead of 4)
          9 => 1,   // ->method3() wrong indentation (8 spaces instead of 4)
          16 => 1,  // ->createQueryBuilder() wrong indentation (0 spaces instead of 6)
          17 => 1,  // ->where() wrong indentation (12 spaces instead of 6)
          18 => 1,  // ->setParameter() wrong indentation (4 spaces instead of 6)
          19 => 1,  // ->getQuery() wrong indentation (14 spaces instead of 6)
          20 => 1   // ->getResult() wrong indentation (10 spaces instead of 6)
        ];

      case 'TwoSpacesIndentSniff.goodClosure.inc':
        return [];

      case 'TwoSpacesIndentSniff.badClosure.inc':
        return [
          7 => 1,   // closureInReturnStatement: return $x * 2; wrong indentation (6 spaces instead of 2)
          17 => 1,  // closureInVariableAssignment: return $item->getValue(); wrong indentation (4 spaces instead of 2)
        ];

      case 'TwoSpacesIndentSniff.goodAttributes.inc':
        return [];

      case 'TwoSpacesIndentSniff.badArrowFunction.inc':
        return [
          5 => 1,   // Arrow function body wrong indentation (4 spaces instead of 2)
          6 => 1,   // 'id' => $item->id, wrong indentation (0 spaces instead of 2)
          11 => 1,  // fn($user) => [ wrong indentation (0 spaces instead of 2)
          12 => 1,  // 'id' => $user->getId() wrong indentation (0 spaces instead of 4)
          13 => 1,  // 'email' => $user->getEmail() wrong indentation (0 spaces instead of 4)
          14 => 1,  // ], wrong indentation (0 spaces instead of 2)
          15 => 1,  // $users wrong indentation (0 spaces instead of 2)
          20 => 1,  // array_map wrong indentation (0 spaces instead of 2)
          21 => 1,  // fn($item) => wrong indentation (0 spaces instead of 4)
          22 => 1,  // ? [ wrong indentation (0 spaces instead of 4)
          23 => 1,  // 'id' => $item->id wrong indentation (0 spaces instead of 6)
          24 => 1,  // 'status' => 'active' wrong indentation (0 spaces instead of 6)
          25 => 1,  // ] wrong indentation (0 spaces instead of 4)
          26 => 1,  // : null, wrong indentation (0 spaces instead of 2)
          27 => 1,  // $items wrong indentation (0 spaces instead of 2)
          28 => 1,  // ), wrong indentation (0 spaces instead of 2)
          29 => 1,  // fn($result) => wrong indentation (0 spaces instead of 2)
          34 => 1,  // ->validateInput($data) wrong indentation (0 spaces instead of 2)
          35 => 1,  // ->transformToArray() wrong indentation (0 spaces instead of 2)
          36 => 1,  // ->process() wrong indentation (0 spaces instead of 2)
          40 => 1,  // ? fn($data) => $this->complexTransformer wrong indentation (0 spaces instead of 2)
          41 => 1,  // ->withOptions(['strict' => true]) wrong indentation (0 spaces instead of 4)
          42 => 1,  // ->transform($data) wrong indentation (0 spaces instead of 4)
          43 => 1,  // : fn($data) => [ wrong indentation (0 spaces instead of 2)
          44 => 1,  // 'raw' => $data wrong indentation (0 spaces instead of 4)
          45 => 1,  // 'processed' => false wrong indentation (0 spaces instead of 4)
          46 => 1,  // ]; wrong indentation (0 spaces instead of 2)
          50 => 1,  // 200 => 'success' wrong indentation (0 spaces instead of 2)
          51 => 1,  // 404 => 'not_found' wrong indentation (0 spaces instead of 2)
          52 => 1,  // default => 'unknown' wrong indentation (0 spaces instead of 2)
        ];

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
