# Scripts Directory

This directory contains utility scripts for development and release management.

## Available Scripts

### `test-commit-hooks.sh`

Tests the Husky commit message validation configuration.

**Usage:**

```bash
scripts/test-commit-hooks.sh
```

**What it does:**

- Tests valid commit message formats
- Tests invalid commit message formats
- Validates the hook configuration
- Reports test results

### `release.sh`

Automates the release process using release-it.

**Usage:**

```bash
# Interactive release (prompts for version type)
scripts/release.sh

# Specific release types
scripts/release.sh patch    # 1.0.0 -> 1.0.1
scripts/release.sh minor    # 1.0.0 -> 1.1.0
scripts/release.sh major    # 1.0.0 -> 2.0.0

# Test without making changes
scripts/release.sh --dry-run
```

**What it does:**

- Validates git status and current branch
- Runs quality checks (`vendor/bin/phing check`)
- Pulls latest changes from origin
- Creates version bump and changelog
- Creates git commit and tag
- Pushes to GitHub and creates release
- Triggers Packagist update

## Requirements

- Node.js and pnpm (for Husky and release-it)
- PHP and Composer (for quality checks)
- Git repository with proper configuration
- Clean working directory for releases

## Permissions

Both scripts are executable (`chmod +x`). If you need to restore permissions:

```bash
chmod +x scripts/*.sh
```
