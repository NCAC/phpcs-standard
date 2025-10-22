# Contributing to NCAC PHPCS Standard

Thank you for your interest in contributing to the NCAC PHPCS Standard! We welcome contributions from the community.

## Getting Started

### Development Environment Setup

For the best development experience, we recommend using VS Code with Dev Containers:

1. **Development Setup**: See [Dev Container Setup Guide](docs/dev-container-setup.md) for detailed instructions
2. **Quick Start**:

   ```bash
   # Clone in WSL2 (Windows users)
   git clone https://github.com/your-username/phpcs-standard.git

   # Generate environment before opening in VS Code
   .docker/generate-env.sh

   # Open in VS Code - Dev Container will auto-configure
   code .
   ```

### Manual Setup

1. **Fork the repository** on GitHub
2. **Clone your fork** locally:
   ```bash
   git clone https://github.com/your-username/phpcs-standard.git
   ```
3. **Install dependencies**:
   ```bash
   composer install
   pnpm install
   ```

## Commit Message Conventions

We use Husky to enforce commit message conventions. All commit messages must follow this format:

### Required Prefixes

Your commit message **must** start with one of these prefixes:

- `feat:` - A new feature or enhancement
- `fix:` - A bug fix or correction
- `chore:` - Maintenance tasks, dependencies, configuration
- `refacto:` - Code refactoring without functional changes
- `docs:` - Documentation updates, README changes, guides
- `release:` - Release preparations, version bumps, changelogs

### Format

```
<prefix>: <description>
```

### Examples

✅ **Valid commit messages:**

```
feat: add new rule for enforcing typed properties
fix: correct parsing error in switch statement sniff
chore: update composer dependencies to latest versions
refacto: simplify variable name validation logic
docs: update installation guide and examples
release: bump version to 1.2.0 and update changelog
```

❌ **Invalid commit messages:**

```
added new feature          # Missing prefix
update: fixed bug          # Invalid prefix 'update:'
feat:added rule            # Missing space after colon
FEAT: new rule             # Prefix must be lowercase
```

### Validation

- Commit messages are automatically validated by Husky pre-commit hooks
- Invalid messages will be rejected with helpful error messages
- The validation runs both locally and in CI/CD

### Tips

- Keep descriptions concise but descriptive
- Use imperative mood ("add" not "adds" or "added")
- Don't end with a period
- Focus on **what** changed, not **how** or **why** (use the commit body for details)

## Development Workflow

### Creating a New Sniff

1. Create your sniff class in the appropriate directory:

   - `NCAC/Sniffs/Formatting/` - for formatting rules
   - `NCAC/Sniffs/NamingConventions/` - for naming rules
   - `NCAC/Sniffs/WhiteSpace/` - for whitespace rules
   - `NCAC/Sniffs/ControlStructures/` - for control structure rules

2. Follow the existing naming pattern: `YourRuleNameSniff.php`

3. Implement the `Sniff` interface with proper documentation

4. Add tests in the `tests/` directory

5. Update the `NCAC/ruleset.xml` file to include your new rule

6. Update the `README.md` with documentation for your rule

### Code Standards

- Follow the NCAC Coding Standard (see `NCAC/ruleset.xml`)
- Use type declarations for all parameters and return values
- Write comprehensive DocBlocks for all public methods
- Include error handling and edge cases
- Ensure your sniff supports automatic fixing when possible

### Quality Check

Before submitting, run the following command to ensure your code passes all tests, static analysis, and coding standards:

```bash
vendor/bin/phing check
```

## Pull Request Process

1. **Create a feature branch** from `main`:

   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes** following the guidelines above

3. **Write or update tests** for your changes

4. **Update documentation** in README.md if needed

5. **Commit your changes** with clear, descriptive messages following our conventions

6. **Push to your fork**:

   ```bash
   git push origin feature/your-feature-name
   ```

7. **Create a Pull Request** on GitHub with:
   - Clear title describing the change
   - Detailed description of what was changed and why
   - References to any related issues
   - Screenshots or examples if applicable

## 📝 Pull Request Template

When opening a pull request, please use the following template:

```markdown
# 🚀 Fix: <Short Description>

## Summary

- <What was changed>
- <Why it was changed>
- <Tests and coverage>

## Details

- <Technical details, if needed>

## Results

- <Impact, coverage, tests passing>

## Closing

Closes #<issue-number>
Branch: `<branch-name>`

---

### Checklist

- [x] All unit tests passing
- [x] Coverage > 75%
- [x] Review requested
```

## Issue Reporting

When reporting issues, please include:

- PHP version
- PHPCS version
- NCAC standard version
- Minimal code example that reproduces the issue
- Expected vs actual behavior
- Full error messages or output

## Code Review Process

All submissions require review. We look for:

- Code quality and standards compliance
- Proper test coverage
- Clear documentation
- Performance considerations
- Backward compatibility

## Release Process

Releases are handled by maintainers and follow semantic versioning:

- **Patch** (1.0.1): Bug fixes, documentation updates
- **Minor** (1.1.0): New features, new sniffs
- **Major** (2.0.0): Breaking changes, major refactoring

### Prerequisites

Before creating a release:

1. **Clean working directory**: All changes must be committed
2. **Main branch**: Must be on the `main` branch
3. **Quality checks**: All tests, PHPCS, and Psalm must pass
4. **Updated documentation**: README and CHANGELOG should be current

### Using the Release Script

We provide a helper script `scripts/release.sh` that automates the release process:

```bash
# Interactive release (prompts for version type)
scripts/release.sh

# Specific release types
scripts/release.sh patch    # 1.0.0 -> 1.0.1
scripts/release.sh minor    # 1.0.0 -> 1.1.0
scripts/release.sh major    # 1.0.0 -> 2.0.0

# Dry-run to test without making changes
scripts/release.sh --dry-run
```

### What the Release Process Does

1. **Validation**: Checks git status and branch
2. **Quality assurance**: Runs `vendor/bin/phing check`
3. **Version bump**: Updates version in relevant files
4. **Changelog**: Auto-generates from conventional commits
5. **Git operations**: Creates commit with `release:` prefix and tags
6. **GitHub release**: Creates GitHub release with changelog
7. **Packagist**: Triggers automatic update via webhook

### Manual Release (Advanced)

If you prefer manual control, you can use `release-it` directly:

```bash
# Interactive release
npx release-it

# Specific version
npx release-it 1.2.0

# Test without changes
npx release-it --dry-run
```

### Commit Message Impact on Changelog

The changelog is automatically generated based on conventional commit prefixes:

- `feat:` → ✨ **Features**
- `fix:` → 🐛 **Bug Fixes**
- `chore:` → 🔧 **Maintenance**
- `refacto:` → ♻️ **Refactoring**
- `docs:` → 📚 **Documentation**
- `release:` → 🚀 **Releases**

### Release Checklist

For maintainers, see the complete [Release Checklist](docs/release-checklist.md) for step-by-step release process.

## Publishing to Packagist

Only the main maintainer (NCAC) is allowed to publish or trigger releases on Packagist. The Packagist API token and username are kept private and are never shared in the repository or with contributors.

- Contributors can submit pull requests and participate in code review.
- Only the maintainer can merge and trigger a release (tag and publish on Packagist).
- The publication process uses GitHub Actions secrets to securely store credentials (see below).

### GitHub Actions Secrets

GitHub Actions secrets are encrypted environment variables used to store sensitive information (like API tokens) securely in your repository settings. They are never exposed in logs or to users without write/admin access.

**How it works:**

- The maintainer adds `PACKAGIST_API_TOKEN` and `PACKAGIST_USERNAME` as secrets in the GitHub repository settings (Settings → Secrets and variables → Actions).
- The CI workflow uses these secrets to authenticate and publish to Packagist during a release.
- Contributors and forks do not have access to these secrets, so only the maintainer can trigger a real publication.

**Best practices:**

- Never commit secrets or tokens in the repository.
- Only maintainers with admin access can add or update secrets.
- Document this process for transparency and security.

---

## Community Guidelines

- Be respectful and inclusive
- Focus on constructive feedback
- Help others learn and grow
- Follow the code of conduct

## Questions?

Feel free to open a discussion or issue if you have questions about contributing!
