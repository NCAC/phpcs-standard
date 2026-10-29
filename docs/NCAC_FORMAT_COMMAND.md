# `ncac-format` reference

`ncac-format` brings PHP code to the NCAC standard in two passes and reports what is left to fix by hand:

1. **PHP-CS-Fixer** applies structural rewrites: alternative syntax to braces, imports, `declare(strict_types=1)`, quotes, etc.
2. **PHPCBF** applies the rules of your PHPCS configuration: indentation, naming, class spacing, etc.
3. **PHPCS** validates the result. Its report lists what needs a manual fix, and its result decides the exit code.

The command is installed with the package as `vendor/bin/ncac-format`. The reasons behind its behaviour are recorded in [ADR 0001](adr/0001-ncac-format-contract.md).

## Synopsis

```
ncac-format [--dry-run | --check] [--] [<path>...]
ncac-format --help
```

| Option | Effect |
|---|---|
| *(none)* | Fix mode: both passes rewrite files, then PHPCS validates. |
| `--dry-run` | Prints the diff of both passes. Writes nothing. |
| `--check` | Runs PHPCS only. Writes nothing. |
| `--` | Ends options: the next arguments are paths, even if they start with `-`. |
| `-h`, `--help` | Prints the help. |

`--dry-run` and `--check` are mutually exclusive. Unknown options are rejected.

## Paths

- Paths are resolved against the **current directory**: `cd web/modules/custom && ncac-format mymodule` works.
- Several paths can be given, and duplicates are ignored. Without a path, the current directory is the target.
- A path that does not exist stops the command with exit code `2`. Nothing runs.
- The tools run from the project root and receive absolute paths.

## Configuration lookup

The **project root** is the closest directory containing a `composer.json`, from the current directory upwards. Without one, it is the current directory. Configuration files are looked up in the project root only.

| Pass | Lookup order | Fallback |
|---|---|---|
| PHPCBF, PHPCS | `.phpcs.xml`, `phpcs.xml`, `.phpcs.xml.dist`, `phpcs.xml.dist` | `--standard=NCAC --ignore=*/vendor/*,*/node_modules/*` |
| PHP-CS-Fixer | `.php-cs-fixer.php`, `.php-cs-fixer.dist.php` | `vendor/ncac/phpcs-standard/.php-cs-fixer.dist.php` |

The header of every run shows which files were used:

```
NCAC format - fix
  Project root : /var/www/project
  PHPCS config : phpcs.xml (project)
  Fixer config : .php-cs-fixer.php (project)
  Paths        : web/modules/custom/mymodule
```

PHP-CS-Fixer always runs with:

- `--path-mode=intersection`: the given paths narrow the Finder of the configuration instead of replacing it, so its exclusions (`vendor`, contrib code, generated files) still apply;
- `--no-interaction`: PHP-CS-Fixer never asks questions. It also always gets an explicit configuration, so it never starts its set-up wizard.

## Exit codes

| Code | Fix mode, `--check` | `--dry-run` |
|---|---|---|
| `0` | PHPCS reports nothing: compliant | no pass would change a file |
| `1` | PHPCS reports violations that need a manual fix | at least one pass would change a file, or PHP-CS-Fixer found a file with invalid syntax |
| `2` | invalid usage | invalid usage |
| `3` | a tool is missing, or failed (configuration error, crash); the remaining steps are skipped | same |

In `--dry-run`, violations that PHPCBF cannot fix do not change the exit code: a dry run reports changes, and `--check` reports violations.

## Recipes

### Composer scripts

```json
{
  "scripts": {
    "format": "ncac-format",
    "format:check": "ncac-format --check"
  }
}
```

```bash
composer format web/modules/custom
composer format -- --dry-run web/modules/custom   # options after --
composer format:check
```

Composer runs scripts from the project root, so script paths are relative to the root. Call `vendor/bin/ncac-format` directly to use paths relative to the current directory.

### Continuous integration

```yaml
# GitHub Actions
- name: Coding standard
  run: vendor/bin/ncac-format --check
```

Use `--check`: it fails exactly when PHPCS reports a violation. `--dry-run` also fails when a pass would only reformat compliant code.

### Git pre-commit hook

```sh
#!/bin/sh
# .git/hooks/pre-commit: check the staged PHP files (file names without spaces)
files=$(git diff --cached --name-only --diff-filter=ACMR -- '*.php' '*.module' '*.inc' '*.install' '*.theme')
[ -z "$files" ] && exit 0
exec vendor/bin/ncac-format --check -- $files
```

The hook checks the files as they are in the working tree, which may differ from what is staged.

### VS Code task

```json
{
  "label": "ncac-format: current file",
  "type": "process",
  "command": "${workspaceFolder}/vendor/bin/ncac-format",
  "args": ["${file}"],
  "problemMatcher": []
}
```

## Migrating from a custom wrapper or `scripts/ncac-fix.sh`

Before 5.1, `ncac-format` and `scripts/ncac-fix.sh` forced `--standard=NCAC` and ignored the project's `phpcs.xml`. Projects worked around it with wrapper scripts that passed `--standard=phpcs.xml` and `--path-mode=intersection` themselves. Those wrappers can now be replaced by the command itself:

```json
{
  "scripts": {
    "ncac-fix": "ncac-format"
  }
}
```

`scripts/ncac-fix.sh` is deprecated. It forwards its arguments to `ncac-format` and will be removed in the next major version.

## Known limitations

- **The pass 2 preview in `--dry-run` is computed on the files as they are**, not on the output of pass 1. The two diffs can overlap, and the real run can differ slightly.
- **Pass 1 applies risky rules.** The shipped PHP-CS-Fixer configuration enables `declare_strict_types` and the `phpdoc_to_*_type` rules, which change behaviour or signatures. On legacy code, review `--dry-run` first, or disable them in your `.php-cs-fixer.php`.
- **The shipped PHP-CS-Fixer configuration does not fully agree with NCAC.** It removes the blank lines that NCAC requires after a class opening brace and before its closing brace, and PHPCBF puts them back in pass 2. The result of a fix run is compliant, but `php-cs-fixer check` with that configuration is not a compliance check: use `ncac-format --check`.
- **Enums** are not covered by the class spacing sniffs: their blank lines after the opening brace and before the closing brace are not enforced.
