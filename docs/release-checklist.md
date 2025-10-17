# Release Checklist ```bash

# Test the release process (recommended first)

scripts/release.sh --dry-run

# Interactive release (asks for version type)

scripts/release.sh

# Direct version release

scripts/release.sh patch # Bug fixes: 1.0.0 -> 1.0.1
scripts/release.sh minor # New features: 1.0.0 -> 1.1.0  
scripts/release.sh major # Breaking changes: 1.0.0 -> 2.0.0

````Standard

## Quick Referenc- `feat:` → ✨ Features

- `fix:` → 🐛 Bug Fixes
- `chore:` → 🔧 Maintenance
- `refacto:` → ♻️ Refactoring
- `docs:` → 📚 Documentation
- `release:` → 🚀 Releases Maintainers

### Before Release

- [ ] All PRs merged to `main` branch
- [ ] Working directory clean (no uncommitted changes)
- [ ] Quality checks pass: `vendor/bin/phing check`
- [ ] Documentation updated if needed
- [ ] Review recent commits for appropriate version bump

### Release Commands

```bash
# Test the release process (recommended first)
scripts/release.sh --dry-run

# Interactive release (asks for version type)
scripts/release.sh

# Direct version release
scripts/release.sh patch    # Bug fixes: 1.0.0 -> 1.0.1
scripts/release.sh minor    # New features: 1.0.0 -> 1.1.0
scripts/release.sh major    # Breaking changes: 1.0.0 -> 2.0.0
````

### What Happens During Release

1. **Validation**: Script checks git status and current branch
2. **Quality**: Runs complete test suite (`vendor/bin/phing check`)
3. **Version**: Bumps version in `composer.json`
4. **Changelog**: Auto-generates from conventional commits
5. **Commit**: Creates commit with `release: v{version}` message
6. **Tag**: Creates git tag `v{version}`
7. **Push**: Pushes commit and tag to GitHub
8. **GitHub Release**: Creates GitHub release with changelog
9. **Packagist**: Webhook automatically updates package

### GitHub Token Setup

For automated GitHub releases, add `GITHUB_TOKEN` to repository secrets:

1. Go to GitHub repository → Settings → Secrets and variables → Actions
2. Click "New repository secret"
3. Name: `GITHUB_TOKEN`
4. Value: Personal access token with `repo` scope

### Version Guidelines

Follow semantic versioning (semver.org):

- **PATCH** (1.0.X): Backwards compatible bug fixes
- **MINOR** (1.X.0): Backwards compatible new features
- **MAJOR** (X.0.0): Breaking changes

### Commit Types for Changelog

Commits are automatically categorized:

- `feat:` → ✨ Features
- `fix:` → 🐛 Bug Fixes
- `chore:` → 🔧 Maintenance
- `refacto:` → ♻️ Refactoring
- `release:` → 🚀 Releases

### Troubleshooting

**"Quality checks failed"**

- Run `vendor/bin/phing check` manually
- Fix any PHPCS, Psalm, or test failures

**"Not on main branch"**

- Switch to main: `git checkout main`
- Pull latest: `git pull origin main`

**"Uncommitted changes"**

- Commit changes: `git add . && git commit -m "..."`
- Or stash: `git stash`

**"No GITHUB_TOKEN"**

- Release will work but GitHub release creation will be manual
- Add token to repository secrets for automation

### Post-Release

- [ ] Verify release appears on GitHub
- [ ] Check Packagist shows new version
- [ ] Announce release if significant
- [ ] Close related issues/milestones
