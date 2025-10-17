# Commit Message Validation - Configuration Summary

## Overview

This project uses Husky to enforce commit message conventions. All commit messages are automatically validated before being accepted.

## Supported Prefixes

- `feat:` - New features or enhancements
- `fix:` - Bug fixes or corrections
- `chore:` - Maintenance tasks, dependencies, configuration
- `refacto:` - Code refactoring without functional changes
- `docs:` - Documentation updates, README changes, guides
- `release:` - Release preparations, version bumps, changelogs

## Validation Rules

1. Message must start with one of the supported prefixes
2. Prefix must be lowercase
3. Must have a colon and space after prefix: `prefix: description`
4. Description must be at least 1 character long

## Testing

Run the test script to validate the hook configuration:

```bash
scripts/test-commit-hooks.sh
```

## Files Modified

- `.husky/commit-msg` - Main validation hook
- `CONTRIBUTING.md` - Documentation for contributors
- `scripts/test-commit-hooks.sh` - Test script for validation
- `package.json` - Husky dependency and scripts

## Examples

### ✅ Valid Messages

```
feat: add new validation rule
fix: correct parsing error in switch statements
chore: update composer dependencies
refacto: simplify variable name logic
docs: update installation guide
release: bump version to 2.0.0
```

### ❌ Invalid Messages

```
added new feature          # Missing prefix
update: fixed bug          # Invalid prefix 'update:'
feat:added rule            # Missing space after colon
FEAT: new rule             # Prefix must be lowercase
```

## Troubleshooting

If commits are rejected:

1. Check that your message starts with a valid prefix
2. Ensure there's a space after the colon
3. Use lowercase for the prefix
4. Run `scripts/test-commit-hooks.sh` to verify configuration
