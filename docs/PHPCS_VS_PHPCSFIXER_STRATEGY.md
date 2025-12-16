# PHPCS vs PHP-CS-Fixer Strategy

## Problem Statement

The NCAC standard faces a common challenge in the PHP ecosystem: **when to use PHPCS for automatic fixing vs when to delegate to PHP-CS-Fixer**.

## Technical Challenge: Token Regeneration

### The Issue

PHP_CodeSniffer processes sniffs sequentially, but **token modifications by one sniff are not always visible to subsequent sniffs**. This creates conflicts when:

1. **Sniff A** modifies tokens using `addContentBefore()` or `replaceToken()`
2. **Sniff B** (running later) operates on the original token stream
3. **Sniff B** overwrites modifications from **Sniff A**, potentially creating invalid syntax

### Real Example: NoAlternateControlStructureSniff

```php
// Original code
if ($condition):
    echo "hello";
elseif ($other):
    echo "world";
endif;
```

**Problem sequence:**
1. `NoAlternateControlStructureSniff` tries to convert `:` → `{` and `endif` → `}`
2. `TwoSpacesIndentSniff` runs later and normalizes whitespace
3. `TwoSpacesIndentSniff` replaces whitespace tokens, **losing the `}` inserted before `elseif`**
4. **Result**: Invalid PHP syntax (missing closing braces)

## Our Solution: Strategic Separation

### PHPCS Role: Detection & Simple Fixes
- ✅ **Whitespace normalization** (indentation, spacing)
- ✅ **Simple token replacements** (single token → single token)
- ✅ **Naming convention detection**
- ✅ **Structure validation**
- ❌ **Complex token insertion/deletion**
- ❌ **Multi-token transformations**

### PHP-CS-Fixer Role: Complex Transformations
- ✅ **Alternate syntax conversion** (if:/endif → if{})
- ✅ **Code restructuring**
- ✅ **Advanced formatting rules**
- ✅ **Multi-line transformations**

## Implementation Strategy

### Current State (NCAC v1.0)
```bash
# Detection only
phpcs --standard=NCAC src/
```

### Future State (NCAC v2.0)
```bash
# 1. Fix complex issues
php-cs-fixer fix --config=.ncac-cs-fixer.php src/

# 2. Fix simple issues + validate
phpcs --standard=NCAC src/ --fix

# 3. Final validation
phpcs --standard=NCAC src/
```

## Decision Matrix

| Type of Rule | Tool | Reason |
|--------------|------|---------|
| Indentation | PHPCS | Simple, reliable |
| Spacing | PHPCS | Single-token operations |
| Naming | PHPCS | Detection-focused |
| **Alternate Syntax** | **PHP-CS-Fixer** | **Multi-token complexity** |
| Code Style | PHP-CS-Fixer | Flexible transformations |
| Modern Syntax | PHP-CS-Fixer | Language evolution support |

## Benefits of This Approach

1. **Reliability**: No token conflicts → no invalid syntax generation
2. **Performance**: Each tool optimized for its use case
3. **Maintainability**: Clear separation of concerns
4. **Flexibility**: Can evolve tools independently
5. **Industry Standard**: Follows established PHP ecosystem patterns

## Examples in the Wild

- **Symfony**: Uses both PHP-CS-Fixer and custom PHPCS rules
- **Laravel**: Primarily PHP-CS-Fixer with PHPCS validation
- **PHPStan**: Pure analysis, delegates formatting to specialized tools

## Migration Path

### Phase 1 (Current): PHPCS Detection-Only
- All complex rules report errors without fixing
- Clear guidance to use manual fixes or wait for Phase 2

### Phase 2 (Planned): Hybrid Approach
- Custom PHP-CS-Fixer configuration for NCAC rules
- PHPCS handles simple fixes and validation
- Integrated workflow with single command

### Phase 3 (Future): IDE Integration
- VS Code extension with integrated workflow
- Real-time fixes via PHP-CS-Fixer
- Live validation via PHPCS

---

*This strategy ensures robust, maintainable code quality tooling while avoiding the technical pitfalls of complex token manipulation in PHPCS.*
