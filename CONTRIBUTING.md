# Contributing to NCAC PHPCS Standard

Thank you for your interest in contributing to the NCAC PHPCS Standard! We welcome contributions from the community.

## Getting Started

1. **Fork the repository** on GitHub
2. **Clone your fork** locally:
   ```bash
   git clone https://github.com/your-username/phpcs-standard.git
   ```
3. **Install dependencies**:
   ```bash
   composer install
   ```

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

## Git and CI/CD Workflow

- Use the git-flow model: work on feature/* branches, merge into develop, then release/* or hotfix/* into main.
- The CI is triggered automatically:
  - on push/merge to develop (continuous integration)
  - on push/merge to main (production)
  - on tag creation (release)
- Build and quality badges are automatically updated in the README.
- To publish a version, create a tag on main: the CI will validate the release and update the badges.

### Example badge to place in README.md:

```
![CI](https://github.com/<OWNER>/<REPO>/actions/workflows/ci.yml/badge.svg?branch=main)
![Release](https://github.com/<OWNER>/<REPO>/actions/workflows/ci.yml/badge.svg?event=push&branch=main)
```

Replace `<OWNER>` and `<REPO>` with your GitHub namespace and repository name.

---

For any contribution, follow this workflow to ensure code robustness and traceability.

## Pull Request Process

1. **Create a feature branch** from `main`:

   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes** following the guidelines above

3. **Write or update tests** for your changes

4. **Update documentation** in README.md if needed

5. **Commit your changes** with clear, descriptive messages:

   ```bash
   git commit -m "Add: New sniff for detecting X pattern"
   ```

6. **Push to your fork**:

   ```bash
   git push origin feature/your-feature-name
   ```

7. **Create a Pull Request** on GitHub with:
   - Clear title describing the change
   - Detailed description of what was changed and why
   - References to any related issues
   - Screenshots or examples if applicable

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
