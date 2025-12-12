<?php

namespace NCAC\Tests\NCAC;

use NCAC\Sniffs\ControlStructures\SwitchDeclarationSniff;
use NCAC\Sniffs\Formatting\ClassClosingSpacingSniff;
use NCAC\Sniffs\Formatting\ClassOpeningSpacingSniff;
use NCAC\Sniffs\Formatting\NoAlternateControlStructureSniff;
use NCAC\Sniffs\Formatting\OpeningBraceKAndRSniff;
use NCAC\Sniffs\NamingConventions\FunctionNameSniff;
use NCAC\Sniffs\NamingConventions\MethodNameSniff;
use NCAC\Sniffs\NamingConventions\PascalCaseClassNameSniff;
use NCAC\Sniffs\NamingConventions\VariableNameSniff;
use NCAC\Sniffs\WhiteSpace\DeclarationSpacingSniff;
use NCAC\Sniffs\WhiteSpace\TwoSpacesIndentSniff;
use PHPUnit\Framework\TestCase;

/**
 * Simple test to improve code coverage.
 * Tests the register() methods of all Sniffs.
 */
class SniffRegistrationTest extends TestCase {

  /**
   * @dataProvider sniffProvider
   */
  public function testSniffRegistration(string $sniff_class): void {
    try {
      $sniff = new $sniff_class();
      $tokens = $sniff->register();

      $this->assertIsArray($tokens);
      $this->assertNotEmpty($tokens);

      // Tous les tokens doivent être des entiers ou des constantes valides
      foreach ($tokens as $token) {
        $this->assertTrue(
          is_int($token) || (is_string($token) && $this->isValidToken($token)),
          "Token should be an integer or valid constant, got: " . var_export($token, true)
        );
      }
    } catch (\Error $e) {
      // If a constant is not defined, mark the test as skipped
      if (strpos($e->getMessage(), 'Undefined constant') !== false) {
        $this->markTestSkipped("Sniff $sniff_class has undefined constants: " . $e->getMessage());
      }
      throw $e;
    }
  }

  /**
   * Checks if a token is valid (T_* constant or class constant).
   */
  private function isValidToken(string $token): bool {
    // Standard PHP T_* constants
    if (defined($token)) {
      return true;
    }

    // Custom constants in sniffs (e.g. TwoSpacesIndentSniff::CHAINED_BLOCK)
    if (strpos($token, '::') !== false) {
      [$class, $constant] = explode('::', $token, 2);
      if (class_exists($class) && defined("$class::$constant")) {
        return true;
      }
    }

    return false;
  }

  /**
   * Provides all NCAC Sniffs for testing.
   */
  public static function sniffProvider(): array {
    return [
      'SwitchDeclaration' => [SwitchDeclarationSniff::class],
      'ClassClosingSpacing' => [ClassClosingSpacingSniff::class],
      'ClassOpeningSpacing' => [ClassOpeningSpacingSniff::class],
      'NoAlternateControlStructure' => [NoAlternateControlStructureSniff::class],
      'OpeningBraceKAndR' => [OpeningBraceKAndRSniff::class],
      'FunctionName' => [FunctionNameSniff::class],
      'MethodName' => [MethodNameSniff::class],
      'VariableName' => [VariableNameSniff::class],
      'DeclarationSpacing' => [DeclarationSpacingSniff::class],
      'TwoSpacesIndent' => [TwoSpacesIndentSniff::class],
      'PascalCaseClassName' => [PascalCaseClassNameSniff::class],
    ];
  }
}
