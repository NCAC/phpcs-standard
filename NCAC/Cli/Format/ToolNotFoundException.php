<?php declare(strict_types=1);

namespace NCAC\Cli\Format;

use RuntimeException;

/**
 * Raised when a required tool (php-cs-fixer, phpcbf, phpcs) cannot be located.
 *
 * @package NCAC\Cli\Format
 */
final class ToolNotFoundException extends RuntimeException {

}
