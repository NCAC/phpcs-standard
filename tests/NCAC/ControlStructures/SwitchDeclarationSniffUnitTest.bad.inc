<?php
/**
 * Test file with INVALID switch statements for SwitchDeclarationSniff
 * This file tests switch structure rules (not keyword case)
 */

// Test 1: Space before colon - line 8
switch ($value) {
  case 1 :  // Space before colon - ERROR
    echo "One";
    break;
  default:
    echo "Default";
    break;
}

// Test 2: Empty case block - line 18
switch ($empty) {
  case 2:  // ERROR - Empty case
  default:
    echo "Default";
    break;
}

// Test 3: Space before colon in default - line 26
switch ($default_space) {
  case 3:
    echo "Three";
    break;
  default:  // Space before colon in default - ERROR
    echo "Default";
    break;
}

// Test 4: Empty default block - line 36
switch ($empty_default) {
  case 4:
    echo "Four";
    break;
  default:  // ERROR - Empty default
}

// Test 5: Blank line before break - line 45
switch ($blank_line) {
  case 5:
    echo "Five";

    break;  // Blank line before break - ERROR
  default:
      break;
}

// Test 6: Missing break - line 55
switch ($missing_break) {
  case 6:
    echo "No break";  // Missing break - ERROR
    // no break
  default:
      break;
}

// Test 7: Missing default case - line 63
switch ($missing_default) {
  case 7:
    echo "Seven";
    break;
}

// Test 8: Missing case (only default) - line 70
switch ($only_default) {
  default:
    echo "Only default";
    break;
}
