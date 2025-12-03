<?php

/**
 * Test that the NCAC standard can be loaded by PHPCS.
 */

namespace NCAC\E2ETests;

class StandardLoadTest extends E2ETest {

  public function getName(): string {
    return "Standard Load Test";
  }

  public function run(): void {
    $this->step("Checking if PHPCS can load the NCAC standard...");
    
    $phpcs = $this->runner->getPhpcsExecutable();
    $standard = $this->runner->getStandard();
    
    $output = shell_exec("{$phpcs} --standard={$standard} -e 2>&1");
    
    $this->runner->assertContains('NCAC', $output, "PHPCS cannot load the NCAC standard");
    $this->success("PHPCS successfully loaded the NCAC standard");
    
    // Check that specific sniffs are registered
    $this->step("Verifying registered sniffs...");
    
    $sniffs = [
      'TwoSpacesIndent',
      'PascalCaseClassName',
      'FunctionName',
      'MethodName',
      'SwitchDeclaration',
    ];
    
    foreach ($sniffs as $sniff) {
      $this->runner->assertContains($sniff, $output, "Sniff '{$sniff}' not found");
    }
    
    $this->success("All expected sniffs are registered (" . count($sniffs) . " sniffs)");
  }
}
