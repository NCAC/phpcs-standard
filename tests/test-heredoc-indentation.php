<?php

declare(strict_types=1);

/**
 * Test file to verify HEREDOC/NOWDOC indentation handling
 */

function test_heredoc_indentation(): void {
  // This HEREDOC should not trigger indentation errors
  $message = <<<HELP
NCAC Code Formatter

Usage: ncac-format [--dry-run] [path]

Options:
  --dry-run    Preview changes without applying them
  --help       Show this help message

Examples:
  ncac-format src/              Format all files in src/
  ncac-format --dry-run src/    Preview changes in src/
  ncac-format                   Format all files in current directory

HELP;

    echo $message;
}

function test_nowdoc_indentation(): void {
  // This NOWDOC should not trigger indentation errors
    $text = <<<'EOT'
This is a NOWDOC string.
No variable interpolation here.
The closing marker must be at column 0.
EOT;

    echo $text;
}

  // Test nested in control structure
if (true) {
    $help = <<<HEREDOC
Some help text
With multiple lines
Must close at column 0
HEREDOC;

  echo $help;
}
