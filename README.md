# NCAC PHPCS Standard

[![Build Status](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml/badge.svg)](https://github.com/ncac/phpcs-standard/actions/workflows/ci.yml)
[![Latest Stable Version](https://img.shields.io/packagist/v/ncac/phpcs-standard)](https://packagist.org/packages/ncac/phpcs-standard)
[![Total Downloads](https://img.shields.io/packagist/dt/ncac/phpcs-standard)](https://packagist.org/packages/ncac/phpcs-standard)
[![PHP Version](https://img.shields.io/packagist/php-v/ncac/phpcs-standard)](https://packagist.org/packages/ncac/phpcs-standard)
[![codecov](https://codecov.io/gh/ncac/phpcs-standard/branch/main/graph/badge.svg)](https://codecov.io/gh/ncac/phpcs-standard)
[![License](https://img.shields.io/github/license/ncac/phpcs-standard)](https://github.com/ncac/phpcs-standard/blob/main/LICENSE)

**An opinionated PHP coding standard, and the command that applies it.**

This package ships two things:

1. **`NCAC`**, a [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer) standard: explicit types everywhere, 2-space indentation, K&R braces, and naming that depends on context (`snake_case` variables and functions, `camelCase` methods and properties, `PascalCase` classes).
2. **`ncac-format`**, a command that brings existing code to that standard in two passes (PHP-CS-Fixer, then PHPCBF) and then tells you exactly what is left to fix by hand.

The standard says what compliant code looks like. `ncac-format` gets your code there.

```bash
composer require --dev ncac/phpcs-standard

vendor/bin/ncac-format src/            # fix, then report what is left
vendor/bin/ncac-format --dry-run src/  # preview the changes, write nothing
vendor/bin/ncac-format --check         # validate only, for CI and git hooks
```

## Contents

- [`ncac-format`](#ncac-format)
- [The NCAC standard](#the-ncac-standard)
- [Configuration](#configuration)
- [Drupal projects](#drupal-projects)
- [Requirements](#requirements)
- [Development](#development)

## `ncac-format`

### Why two passes

PHPCBF fixes code token by token, one sniff after the other, on a shared token stream. That is fine for local fixes (indentation, spacing, renaming) but unsafe for structural rewrites: turning `if (...): ... endif;` into braces, removing unused imports or adding `declare(strict_types=1)` can be undone or broken by the sniffs that run next. This is why some NCAC sniffs, such as `NoAlternateControlStructure`, only *detect* problems.

PHP-CS-Fixer is built for structural rewrites. So `ncac-format` runs:

```
                 pass 1                    pass 2                validation
your code ──▶ PHP-CS-Fixer ──▶ PHPCBF (your PHPCS config) ──▶ PHPCS ──▶ exit code
              structural         NCAC rules: indentation,       what is left
              rewrites           naming, class spacing, ...     to fix by hand
```

PHPCS is the reference: code is compliant when PHPCS reports nothing. PHP-CS-Fixer is only a means to get there.

### Modes

| Command | Writes files | Runs | Use it for |
|---|---|---|---|
| `ncac-format [<path>...]` | yes | Fixer, PHPCBF, PHPCS | everyday formatting |
| `ncac-format --dry-run [<path>...]` | no | Fixer and PHPCBF as diffs | reviewing what would change |
| `ncac-format --check [<path>...]` | no | PHPCS | CI, git hooks |

Paths are relative to the current directory. Several paths can be given, and the default is the current directory. A path that does not exist is an error.

### Configuration lookup

`ncac-format` looks for configuration files in the project root, which is the closest directory containing a `composer.json`, starting from the current directory.

| Pass | First file found, in this order | Without one |
|---|---|---|
| PHPCBF and PHPCS | `.phpcs.xml`, `phpcs.xml`, `.phpcs.xml.dist`, `phpcs.xml.dist` (PHP_CodeSniffer's own order) | the `NCAC` standard, with `vendor/` and `node_modules/` ignored |
| PHP-CS-Fixer | `.php-cs-fixer.php`, `.php-cs-fixer.dist.php` | the [configuration shipped with this package](.php-cs-fixer.dist.php) |

Your configuration always wins: sniff properties, exclusions and file extensions from your `phpcs.xml` apply to both PHPCBF and PHPCS. PHP-CS-Fixer runs with `--path-mode=intersection`, so the paths you pass narrow your Finder instead of replacing it, and its exclusions still apply.

### Exit codes

| Code | Fix and `--check` | `--dry-run` |
|---|---|---|
| `0` | compliant | nothing would change |
| `1` | violations remain and need a manual fix | at least one pass would change a file |
| `2` | invalid usage (unknown option, conflicting modes, missing path) | same |
| `3` | a tool is missing or failed; the run stops at the failing step | same |

See the [`ncac-format` reference](docs/NCAC_FORMAT_COMMAND.md) for Composer scripts, CI, git hooks, editor tasks and known limitations.

## The NCAC standard

### Explicit types

Parameters, return values and properties must be typed (Slevomat `TypeHints` rules).

```php
// ❌
function process($data) {
  return $data->value;
}

// ✅
function process(DataObject $data): string {
  return $data->value;
}
```

### Naming depends on context

| Element | Convention | Example |
|---|---|---|
| Classes, interfaces, traits | `PascalCase` | `UserRepository` |
| Methods | `camelCase` | `findUser()` |
| Properties | `camelCase` | `$this->connectionString` |
| Functions | `snake_case` | `mymodule_helper()` |
| Parameters and local variables | `snake_case` | `$user_id`, `$query_result` |
| Constants | `UPPER_CASE` | `CACHE_TTL` |

### Layout

2-space indentation, K&R braces, one blank line after a class opening brace and before its closing brace, one blank line between members, and class members ordered by kind and visibility.

```php
class UserRepository {

  public const CACHE_TTL = 3600;

  private string $connectionString;

  public function findUser(int $user_id): ?User {
    $query_result = $this->executeQuery($user_id);
    if ($query_result !== null) {
      return $this->mapToUser($query_result);
    }
    return null;
  }

}
```

### Rules

The `NCAC` standard combines 11 custom sniffs with selected [Slevomat](https://github.com/slevomat/coding-standard), Generic and Squiz rules ([ruleset](NCAC/ruleset.xml), [rules reference](docs/rules-reference.md)).

| Custom sniff | Enforces | Auto-fix |
|---|---|---|
| `NCAC.WhiteSpace.TwoSpacesIndent` | 2-space indentation | yes |
| `NCAC.WhiteSpace.DeclarationSpacing` | one space after `function`, `class`, `interface`, `trait`, `enum` | yes |
| `NCAC.Formatting.OpeningBraceKAndR` | opening brace on the declaration line | yes |
| `NCAC.Formatting.ClassOpeningSpacing` | blank lines after a class opening brace | yes |
| `NCAC.Formatting.ClassClosingSpacing` | blank lines before a class closing brace | yes |
| `NCAC.Formatting.NoAlternateControlStructure` | no `if: ... endif;` syntax | no (pass 1 fixes it) |
| `NCAC.ControlStructures.SwitchDeclaration` | `switch` structure: `default` required, every block ends with `break` | no |
| `NCAC.NamingConventions.PascalCaseClassName` | `PascalCase` classes, interfaces, traits | yes |
| `NCAC.NamingConventions.MethodName` | `camelCase` methods | yes |
| `NCAC.NamingConventions.FunctionName` | `snake_case` functions | yes |
| `NCAC.NamingConventions.VariableName` | contextual variable naming (see above) | yes |

## Configuration

Create a `phpcs.xml` in your project root. `ncac-format`, `phpcs` and `phpcbf` all pick it up.

```xml
<?xml version="1.0"?>
<ruleset name="My project">
  <file>src</file>
  <file>tests</file>

  <rule ref="NCAC">
    <!-- Relax a rule during a migration -->
    <exclude name="SlevomatCodingStandard.TypeHints.ParameterTypeHint"/>
  </rule>
</ruleset>
```

Configurable sniff properties:

| Sniff | Property | Default | Effect |
|---|---|---|---|
| `NCAC.NamingConventions.FunctionName` | `allowDoubleUnderscore` | `false` | allows `__` in function names (Drupal hooks) |
| `NCAC.NamingConventions.FunctionName` | `allowLeadingUnderscore` | `false` | allows a leading `_` (Drupal internal functions) |
| `NCAC.Formatting.ClassOpeningSpacing` | `linesCount` | `1` | blank lines after a class opening brace |
| `NCAC.Formatting.ClassClosingSpacing` | `linesCount` | `1` | blank lines before a class closing brace |

The Slevomat rules keep their own properties, for example:

```xml
<rule ref="SlevomatCodingStandard.Classes.PropertySpacing">
  <properties>
    <property name="minLinesCountBeforeWithComment" value="1"/>
    <property name="maxLinesCountBeforeWithComment" value="1"/>
  </properties>
</rule>
```

To customize PHP-CS-Fixer, create a `.php-cs-fixer.php` that starts from the shipped configuration (see [Drupal projects](#drupal-projects) for an example).

## Drupal projects

Drupal hook names contain `__` (`mymodule_preprocess_node__article`) and internal functions start with `_`. With the default NCAC rules, PHPCBF would rename them and break the hooks. A Drupal project needs both configuration files below; `ncac-format` then applies them to both passes.

`phpcs.xml`:

```xml
<?xml version="1.0"?>
<ruleset name="Drupal project">
  <arg name="extensions" value="php,module,inc,install,theme,profile,engine"/>

  <exclude-pattern>web/core/*</exclude-pattern>
  <exclude-pattern>web/modules/contrib/*</exclude-pattern>
  <exclude-pattern>web/themes/contrib/*</exclude-pattern>
  <exclude-pattern>*/vendor/*</exclude-pattern>
  <exclude-pattern>*/node_modules/*</exclude-pattern>

  <rule ref="NCAC"/>

  <rule ref="NCAC.NamingConventions.FunctionName">
    <properties>
      <property name="allowDoubleUnderscore" value="true"/>
      <property name="allowLeadingUnderscore" value="true"/>
    </properties>
  </rule>
</ruleset>
```

`.php-cs-fixer.php`, so that PHP-CS-Fixer also handles Drupal file extensions and skips core and contrib code:

```php
<?php declare(strict_types=1);

/** @var PhpCsFixer\Config $config */
$config = require __DIR__ . '/vendor/ncac/phpcs-standard/.php-cs-fixer.dist.php';

$config->getFinder()
  ->name(['*.module', '*.inc', '*.install', '*.theme', '*.profile', '*.engine'])
  ->exclude(['web/core', 'web/modules/contrib', 'web/themes/contrib', 'web/sites']);

return $config;
```

Then, from anywhere in the project:

```bash
vendor/bin/ncac-format web/modules/custom/mymodule
```

## Requirements

- PHP 8.2 or later (CI runs 8.2, 8.3 and 8.4)
- PHP_CodeSniffer 4, PHP-CS-Fixer 3 and Slevomat Coding Standard 8, installed as dependencies

Older PHP versions: use `^4.1` for PHP 8.1 and `^1.2` for PHP 7.4. See the [changelog](CHANGELOG.md).

## Development

```bash
composer install
composer test        # PHPUnit
composer e2e-test    # end-to-end workflow tests
vendor/bin/phing check
```

See [CONTRIBUTING.md](CONTRIBUTING.md). Design decisions are recorded in [docs/adr](docs/adr/).

NCAC builds on [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer), [Slevomat Coding Standard](https://github.com/slevomat/coding-standard) and [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer).

## License

MIT, see [LICENSE](LICENSE).
