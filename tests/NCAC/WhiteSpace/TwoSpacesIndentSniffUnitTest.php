<?php

namespace NCAC\Tests\WhiteSpace;

use NCAC\Tests\SniffUnitTest;

/**
 * Unit test class for the TwoSpacesIndentSniff.
 *
 * @package NCAC\Tests\WhiteSpace
 */
class TwoSpacesIndentSniffUnitTest extends SniffUnitTest
{
    /**
     * Returns the lines where errors should occur for each fixture file.
     *
     * @param string $test_file The name of the file being tested.
     * @return array<int, int>
     */
    public function getErrorList(string $test_file): array
    {
        switch ($test_file) {

            case 'TwoSpacesIndentSniff.test':
                return [];
                break;

            case 'TwoSpacesIndentSniff.goodDocBlock.inc':
                return [];
                break;

            case 'TwoSpacesIndentSniff.badDocBlock.inc':
                return [
                  2 => 1,
                  3 => 1,
                  4 => 1,
                  5 => 1
                ];
                break;

            case 'TwoSpacesIndentSniff.badFunction.inc':
                return [
                  4 => 1,
                  5 => 1,
                  6 => 1,
                  7 => 1
                ];
                break;

            case 'TwoSpacesIndentSniff.badClass.inc':
                return [
                  8 => 1,
                  10 => 1,
                  12 => 1,
                  13 => 1,
                  14 => 1
                ];
                break;

            case 'TwoSpacesIndentSniff.goodClass.inc':
                return [];
                break;

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
                break;

            case 'TwoSpacesIndentSniff.goodTernaryExpression.inc':
                return [];
                break;

            case 'TwoSpaces.badMultilineConditions.inc':
                return [
                  5 => 1,
                  6 => 1,
                  7 => 1
                ];
                break;

            case 'TwoSpacesIndentSniff.badMultilineCondition.inc':
                return [
                  // if multiline condition
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
                break;

            case 'TwoSpaces.goodMultilineConditions.inc':
                return [];
                break;

        }
        return [];
    }

    /**
     * Returns the lines where warnings should occur for each fixture file.
     *
     * @param string $test_file The name of the file being tested.
     * @return array<int, int>
     */
    public function getWarningList(string $test_file): array
    {
        return [];
    }

    /**
     * Override the standard to use a specific ruleset.
     *
     * @return string
     */
    public function getStandard(): string
    {
        return __DIR__ . '/ruleset.whiteSpace.twoSpacesIndent.xml';
    }

    /**
     * @dataProvider fixtureProvider
     * @testdox Fixture with $fixture_file
     * Runs each fixture individually using the parent implementation.
     *
     * @param string $fixture_file
     */
    public function testFixture(string $fixture_file)
    {
        parent::testFixture($fixture_file);
    }
}
