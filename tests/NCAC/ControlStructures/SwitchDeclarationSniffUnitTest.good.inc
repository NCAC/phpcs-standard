<?php
/**
 * Test file with VALID switch statements for SwitchDeclarationSniff
 *
 * This file contains properly formatted switch statements that should
 * NOT trigger any errors from the SwitchDeclarationSniff.
 */

// Valid switch with lowercase keywords, proper spacing, and breaks
switch ($value) {
  case 1:
    echo "One";
    break;
  case 2:
    echo "Two";
    break;
  default:
    echo "Other";
    break;
}

// Valid switch with multiple cases
switch ($status) {
  case 'active':
    $result = true;
    break;
  case 'inactive':
  case 'disabled':
    $result = false;
    break;
  default:
    $result = null;
    break;
}

// Valid nested switch statements
switch ($category) {
  case 'user':
    switch ($action) {
      case 'create':
        createUser();
        break;
      case 'delete':
        deleteUser();
        break;
      default:
        logError();
        break;
    }
    break;
  case 'admin':
    adminAction();
    break;
  default:
    defaultAction();
    break;
}

// Valid switch with comments in default
switch ($type) {
  case 'simple':
    processSimple();
    break;
  default:
    // Intentionally empty - no processing needed
    break;
}
