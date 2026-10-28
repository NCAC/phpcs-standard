# ADR 0001: `ncac-format` contract

- Status: accepted
- Date: 2026-10-28
- Applies to: `bin/ncac-format`, `NCAC\Cli\Format`, `.php-cs-fixer.dist.php`

## Context

`ncac-format` chains PHP-CS-Fixer, PHPCBF and PHPCS. Up to 5.0.1:

- PHPCBF and PHPCS received `--standard=NCAC`, which makes PHP_CodeSniffer ignore the project's `phpcs.xml`. Sniff properties set there, such as `allowDoubleUnderscore` for Drupal hooks, were silently ignored by the command, so pass 2 renamed `mymodule_preprocess_node__article` to `mymodule_preprocess_node_article`.
- Without a project PHP-CS-Fixer configuration, PHP-CS-Fixer ran without `--config`, which starts its interactive set-up wizard. The command hung on a terminal. Without a terminal, the wizard wrote `.php-cs-fixer.dist.php` and `.gitignore` into the project.
- The given path replaced the PHP-CS-Fixer Finder, dropping its exclusions. The shipped configuration started its Finder at `__DIR__`, which is `vendor/ncac/phpcs-standard` in an installed project, where it found none of the project's files.
- Paths were resolved against the project root after a `chdir`, not against the caller's directory, and were not validated.
- `scripts/ncac-fix.sh` duplicated the command in bash with the same defects.

Projects worked around these defects with their own wrapper scripts.

## Decision

The command follows the rules below. Each one is covered by a test in `tests/NCAC/Cli/Format`.

### Reference

- R1. PHPCS **MUST** be the only judge of compliance. The exit code of fix mode and of `--check` **MUST** derive from PHPCS alone.
- R2. PHP-CS-Fixer is a means of correction, not a criterion. Its findings **MUST NOT** make fix mode or `--check` fail.

### Configuration

- R3. The project root is the closest directory containing a `composer.json`, from the working directory upwards, and the working directory otherwise.
- R4. PHPCBF and PHPCS **MUST** use the first file found in the project root among `.phpcs.xml`, `phpcs.xml`, `.phpcs.xml.dist`, `phpcs.xml.dist` (PHP_CodeSniffer's own order). Only without one **MAY** the command fall back to the `NCAC` standard, ignoring `vendor/` and `node_modules/`.
- R5. PHP-CS-Fixer **MUST** use the first file found in the project root among `.php-cs-fixer.php`, `.php-cs-fixer.dist.php`, and the shipped configuration otherwise. It **MUST NOT** run without `--config`, and **MUST** run with `--no-interaction`.
- R6. PHP-CS-Fixer **MUST** run with `--path-mode=intersection`, so that the configured Finder, and its exclusions, still apply.
- R7. The shipped configuration **MUST** look files up from the working directory (the project root), not from its own directory.

### Invocation

- R8. Paths **MUST** be resolved against the caller's working directory. Every path **MUST** exist, otherwise nothing runs.
- R9. At most one mode option is accepted. Unknown options are rejected.
- R10. Tools **MUST** be started without a shell (an argv array), and only from Composer bin directories, never from the `PATH`.

### Exit codes

- R11. `0` compliant / nothing to change, `1` violations / pending changes, `2` usage error, `3` tool missing or failed. These values are public API: changing them is a breaking change.
- R12. A tool failure (an exit code bit outside the tool's documented finding bits) **MUST** stop the run at that step.
- R13. In `--dry-run`, only pending changes count. Non-fixable PHPCS violations **MUST NOT** change the exit code: reporting them is the job of `--check`.

## Consequences

- A project's `phpcs.xml` and `.php-cs-fixer.php` apply identically whether the tools are run directly or through `ncac-format`. Wrapper scripts are no longer needed.
- The command can serve as a CI gate and a git hook (`--check`).
- Taking the project configuration first is a behaviour change for projects that had a `phpcs.xml` and relied on the command ignoring it.
- R1 hides a real inconsistency, recorded here so that it gets fixed: the shipped PHP-CS-Fixer configuration removes the blank lines that `NCAC.Formatting.ClassOpeningSpacing` and `ClassClosingSpacing` require (`no_blank_lines_after_class_opening`, `class_attributes_separation`). Fix mode converges because PHPCBF runs last, but `php-cs-fixer check` with the shipped configuration fails on compliant code. The target invariant is: *for any file that PHPCS accepts, the shipped PHP-CS-Fixer configuration changes nothing*. Once it holds, `--check` could also run PHP-CS-Fixer.
- The pass 2 preview of `--dry-run` runs on the files before pass 1. An exact preview would need to run both passes on a copy of the targets. That is not done, because exclusion patterns depend on absolute paths.
