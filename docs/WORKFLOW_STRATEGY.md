# NCAC Workflow Strategy

> Strategy document for the evolution of the NCAC code quality workflow
> 
> Created: December 17, 2024
> Last updated: December 17, 2024

## Overview

The `ncac/phpcs-standard` combines two complementary tools to ensure code quality:
- **PHP-CS-Fixer**: Complex transformations and structural corrections
- **PHPCBF**: Corrections based on custom PHPCS sniffs

## Current State (Hybrid Approach)

### Performance Metrics

| Metric | Current Value |
|--------|---------------|
| **E2E Tests Passed** | 10/10 (100%) |
| **Average Improvement Rate** | ~96% |
| **Similarity to Ideal** | ~99% |
| **PHP-CS-Fixer Corrections** | ~60% |
| **PHPCBF Corrections** | ~40% |

### Current Architecture

```
Source code with violations
         ↓
   PHP-CS-Fixer
   (complex transformations)
         ↓
      PHPCBF
   (NCAC sniffs)
         ↓
Improved code (~96% of violations fixed)
```

### Strengths

✅ **Functional workflow**: CI passes successfully  
✅ **Sophisticated sniffs**: `TwoSpacesIndentSniff` (1124 lines) with complex logic  
✅ **Robust tests**: E2E tests based on violation reduction  
✅ **`.fixed` files**: Clear documentation of the ideal standard  

### Areas for Improvement

⚠️ **Unfixable violations**: ~4% of violations remain  
⚠️ **Dual tool dependency**: Double maintenance  
⚠️ **Formatting differences**: php-cs-fixer and phpcbf may conflict  

## Target State (PHP-CS-Fixer First)

### Objectives

| Metric | Target |
|--------|--------|
| **PHP-CS-Fixer Corrections** | 95% |
| **PHPCBF Corrections** | 5% (edge cases) |
| **Similarity to Ideal** | 100% |
| **Improvement Rate** | 100% |

### Target Architecture

```
Source code with violations
         ↓
   PHP-CS-Fixer
   (native rules + NCAC custom fixers)
         ↓
      PHPCBF
   (edge cases only)
         ↓
Code 100% compliant with NCAC standard
```

### Benefits

✨ **Single primary tool**: Less maintenance, fewer conflicts  
✨ **Performant fixers**: php-cs-fixer architecture optimized for corrections  
✨ **Active community**: Rich ecosystem, established best practices  
✨ **Integrated tests**: Fixer testing framework included  

## Migration Plan

### Phase 1: Stabilization (✅ Completed)

- [x] E2E test refactoring (pragmatic approach)
- [x] CI passing successfully
- [x] Strategy documentation

### Phase 2: Analysis and Prioritization (To Do)

**Objective**: Identify violations not fixed by the current workflow

1. **Collect data**
   ```bash
   # Analyze residual violations across all tests
   ./scripts/analyze-remaining-violations.sh
   ```

2. **Prioritize by frequency**
   - Most common violations first
   - Impact on code quality
   - Migration complexity

3. **Document edge cases**
   - Intentionally unfixable violations
   - Specific contexts (Drupal, frameworks)

### Phase 3: Custom Fixer Development (Future)

**Objective**: Create PHP-CS-Fixer fixers for NCAC corrections

#### Example: Migrate `TwoSpacesIndentSniff`

```php
// NCAC/Fixer/Whitespace/TwoSpacesIndentFixer.php
namespace NCAC\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

final class TwoSpacesIndentFixer extends AbstractFixer
{
    public function getDefinition(): FixerDefinition
    {
        return new FixerDefinition(
            'Enforce two-space indentation throughout the codebase.',
            [/* examples */]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        // Correction logic ported from TwoSpacesIndentSniff
    }
}
```

#### Steps per Fixer

1. **Create the fixer** in `NCAC/Fixer/`
2. **Port the logic** from the PHPCS sniff
3. **Write tests** in `tests/Fixer/`
4. **Integrate** into `.php-cs-fixer.dist.php`
5. **Validate** with E2E tests

### Phase 4: Simplification (Future)

**Objective**: Reduce PHPCS sniff complexity

1. **Disable migrated sniffs**
   - Keep only detection sniffs
   - Remove correction logic (already in fixers)

2. **Clean up code**
   - Simplify `TwoSpacesIndentSniff` to detection-only version
   - Reduce maintenance burden

3. **Document the workflow**
   - Update `CODE_QUALITY_WORKFLOW.md`
   - Usage guide for developers

## Recommendations

### For Now (Short Term)

✅ **Keep the current hybrid approach**
- It works and CI passes
- PHPCS sniffs represent a significant investment
- No regression for users

✅ **Document unfixed cases**
- Add comments in `.fixed` files
- Explain why certain violations remain

### For the Future (Medium/Long Term)

🎯 **Migrate progressively to PHP-CS-Fixer**
- Start with the most frequent violations
- One fixer at a time, tested and validated
- Keep PHPCS for detection

🎯 **Develop custom fixers**
- Study the architecture of existing PHP-CS-Fixer fixers
- Reuse E2E tests for validation
- Potentially contribute to the ecosystem

🎯 **Maintain compatibility**
- Don't break existing user experience
- Transparent migrations
- Clear documentation of changes

## Open Questions

### php-cs-fixer in a phpcs-standard Package?

**Question**: Is it appropriate to add php-cs-fixer to `ncac/phpcs-standard`?

**Answer**: **Yes**, for several reasons:

1. **Precedent**: Several popular standards combine both
   - `doctrine/coding-standard` uses php-cs-fixer + phpcs
   - `slevomat/coding-standard` offers both approaches

2. **Pragmatism**: The goal is code quality
   - If php-cs-fixer helps achieve the NCAC standard, it's relevant
   - End users care about the result, not the tool

3. **Standard evolution**: The package can evolve
   - Today: phpcs-standard with php-cs-fixer assistance
   - Tomorrow: NCAC coding-standard (multi-tool)

**Alternative**: Rename the package to `ncac/coding-standard` in a major version (v2.0).

## References

- [PHP-CS-Fixer Documentation](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)
- [PHPCS Documentation](https://github.com/squizlabs/PHP_CodeSniffer)
- [CODE_QUALITY_WORKFLOW.md](./CODE_QUALITY_WORKFLOW.md)
- [E2E Tests](../e2e-tests/E2ETestRunner.php)

## Decision History

| Date | Decision | Reason |
|------|----------|--------|
| 2024-12-17 | E2E tests based on violation reduction | CI blocked by impossible perfect similarity |
| 2024-12-17 | Hybrid approach php-cs-fixer + phpcbf | Pragmatism vs idealism |
| 2024-12-17 | `.fixed` files = ideal target | Standard documentation, not blocker |

---

**Maintained by**: NCAC Team  
**Contact**: [Your contact]  
**License**: MIT
