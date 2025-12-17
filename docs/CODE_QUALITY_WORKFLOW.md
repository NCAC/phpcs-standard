# NCAC Code Quality Workflow

## 🎯 **3-Step Quality Process**

The NCAC standard implements a 3-step approach to ensure optimal code quality:

### **Step 1: PHP-CS-Fixer** (Complex Transformations)
```bash
php-cs-fixer fix --config=vendor/ncac/phpcs-standard/.php-cs-fixer.dist.php
```

**Responsibilities:**
- ✅ Alternative syntax conversion (`if(): endif;` → `if() {}`)
- ✅ Automatic `declare(strict_types=1)` injection
- ✅ Code modernization (arrays, types, etc.)
- ✅ Complex transformations too risky for PHPCS

### **Step 2: PHPCBF** (Simple PHPCS Corrections)
```bash
phpcbf --standard=NCAC
```

**Responsibilities:**
- ✅ Spacing and indentation corrections
- ✅ Naming convention fixes (PascalCase, camelCase)
- ✅ Class structure formatting
- ✅ All auto-fixable PHPCS rules

### **Step 3: PHPCS** (Final Validation)
```bash
phpcs --standard=NCAC
```

**Responsibilities:**
- ✅ Complete compliance validation
- ✅ Detection of violations requiring manual correction
- ✅ Primarily: **missing type hints**

---

## 🚀 **Complete Automated Workflow**

```bash
#!/bin/bash
# NCAC Code Quality Script

echo "🔧 Step 1/3: PHP-CS-Fixer (complex transformations)"
php-cs-fixer fix --config=vendor/ncac/phpcs-standard/.php-cs-fixer.dist.php

echo "🔧 Step 2/3: PHPCBF (PHPCS corrections)"
phpcbf --standard=NCAC

echo "✅ Step 3/3: PHPCS (final validation)"
phpcs --standard=NCAC

if [ $? -eq 0 ]; then
  echo "🎉 Code fully compliant with NCAC standard!"
else
  echo "⚠️  Manual corrections required (primarily type hints)"
fi
```

---

## 📋 **Manual Corrections Required**

After the 3 automated steps, remaining violations are typically:

### **Missing Type Hints**
```php
// ❌ Detected by final PHPCS
public function process($data) {
  return $data;
}

// ✅ Manual correction required  
public function process(array $data): array {
  return $data;
}
```

### **Missing Visibility Modifiers**
```php
// ❌ Detected by final PHPCS
const CONSTANT = 'value';

// ✅ Manual correction required
public const CONSTANT = 'value';
```

---

## 🎯 **Workflow Advantages**

1. **Separation of Concerns**: Each tool handles what it does best
2. **Maximum Automation**: Minimal manual intervention required
3. **Safety**: Risky transformations by PHP-CS-Fixer, simple fixes by PHPCS
4. **Robust Validation**: Guaranteed final compliance
5. **Predictable Results**: Consistent output every time

---

## 💡 **CI/CD Integration**

```yaml
# .github/workflows/quality.yml
name: Code Quality

on: [push, pull_request]

jobs:
  quality:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v4
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
    
    - name: Install dependencies
      run: composer install
    
    - name: PHP-CS-Fixer
      run: php-cs-fixer fix --config=vendor/ncac/phpcs-standard/.php-cs-fixer.dist.php --dry-run --diff
    
    - name: PHPCBF  
      run: phpcbf --standard=NCAC --dry-run
      
    - name: PHPCS Final Validation
      run: phpcs --standard=NCAC
```

---

## 🔄 **Pre-commit Hook Integration**

```bash
#!/bin/sh
# .git/hooks/pre-commit

echo "Running NCAC quality checks..."

# Get list of PHP files in commit
FILES=$(git diff --cached --name-only --diff-filter=ACM | grep '\.php$')

if [ -z "$FILES" ]; then
    echo "No PHP files to check."
    exit 0
fi

echo "Checking files: $FILES"

# Run the 3-step workflow on changed files only
echo "Step 1: PHP-CS-Fixer"
php-cs-fixer fix --config=vendor/ncac/phpcs-standard/.php-cs-fixer.dist.php $FILES

echo "Step 2: PHPCBF"
phpcbf --standard=NCAC $FILES

echo "Step 3: PHPCS validation"
phpcs --standard=NCAC $FILES

if [ $? -ne 0 ]; then
    echo "❌ NCAC quality checks failed. Please fix violations before committing."
    exit 1
fi

# Re-stage files modified by fixers
git add $FILES

echo "✅ All quality checks passed!"
```

---

## 📊 **Quality Metrics**

The NCAC workflow ensures:

- **90%+ Automated Fixes**: Most violations resolved automatically
- **Type Safety**: Strict typing enforcement through Slevomat rules
- **Modern PHP**: Leverages PHP 8.1+ features and best practices
- **Consistent Formatting**: Uniform code style across projects
- **Performance**: Parallel processing where possible
