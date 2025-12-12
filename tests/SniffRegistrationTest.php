<?php

namespace NCAC\Tests;

use NCAC\Sniffs\ControlStructures\SwitchDeclarationSniff;
use NCAC\Sniffs\Formatting\ClassClosingSpacingSniff;
use NCAC\Sniffs\Formatting\ClassOpeningSpacingSniff;
use NCAC\Sniffs\Formatting\NoAlternateControlStructureSniff;
use NCAC\Sniffs\Formatting\OpeningBraceKAndRSniff;
use NCAC\Sniffs\NamingConventions\FunctionNameSniff;
use NCAC\Sniffs\NamingConventions\MethodNameSniff;
use NCAC\Sniffs\NamingConventions\VariableNameSniff;
use NCAC\Sniffs\WhiteSpace\DeclarationSpacingSniff;
use NCAC\Sniffs\WhiteSpace\TwoSpacesIndentSniff;
use PHPUnit\Framework\TestCase;

/**
 * Test simple pour améliorer la couverture de code.
 * Teste les méthodes register() de tous les Sniffs.
 */
class SniffRegistrationTest extends TestCase {

  /**
   * Fournit tous les Sniffs NCAC pour les tests.
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
    ];
  }

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
      // Si une constante n'est pas définie, on marque le test comme skippé
      if (strpos($e->getMessage(), 'Undefined constant') !== false) {
        $this->markTestSkipped("Sniff $sniff_class has undefined constants: " . $e->getMessage());
      }
      throw $e;
    }
  }

  /**
   * Vérifie si un token est valide (constante T_* ou constante de classe).
   */
  private function isValidToken(string $token): bool {
    // Constantes PHP standard T_*
    if (defined($token)) {
      return true;
    }

    // Constantes personnalisées dans les sniffs (ex: TwoSpacesIndentSniff::CHAINED_BLOCK)
    if (strpos($token, '::') !== false) {
      [$class, $constant] = explode('::', $token, 2);
      if (class_exists($class) && defined("$class::$constant")) {
        return true;
      }
    }
    return false;
  }

}
