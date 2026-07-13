<?php declare(strict_types = 1);

error_reporting(E_ALL);

require 'vendor/autoload.php';

// Define PHP 8.4+ token polyfills via nikic/php-parser BEFORE PHP_CodeSniffer's
// Tokens.php gets a chance to define its own (string-typed) versions. Both libraries
// only skip defining a token if it already exists, so whichever runs first "wins";
// php-parser insists these be ints and aborts otherwise, so it must go first here.
// See https://github.com/nikic/PHP-Parser/blob/master/lib/PhpParser/compatibility_tokens.php
require 'vendor/nikic/php-parser/lib/PhpParser/compatibility_tokens.php';

require 'tests/NCAC/SniffUnitTest.php';
require 'vendor/squizlabs/php_codesniffer/autoload.php';
