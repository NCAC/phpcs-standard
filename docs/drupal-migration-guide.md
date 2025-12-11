# NCAC Migration Guide for Drupal Projects

## 🎯 Objective

This guide helps you migrate your Drupal project to the NCAC coding standard while preserving compatibility with Drupal conventions (hooks with `__`, etc.).

## ⚠️ Problem Solved

**Before NCAC v3.0.2**, PHPCBF broke Drupal hooks:

```php
// ✅ BEFORE (valid code)
function mymodule_preprocess_node__homepage(&$variables) {
  // This hook targets node--homepage.html.twig
}

// ❌ AFTER phpcbf (broken!)
function mymodule_preprocess_node_homepage(&$variables) {
  // This hook will NEVER be called by Drupal!
}
```

**With NCAC v3.0.2+**, hooks are preserved thanks to the new `allowDoubleUnderscore` and `allowLeadingUnderscore` options.

---

## 📋 Migration Steps

### 1. Install NCAC in your Drupal project

```bash
cd /path/to/your/drupal/project
composer require --dev ncac/phpcs-standard
```

### 2. Create the `phpcs.xml.dist` file

Create a `phpcs.xml.dist` file at the root of your project:

```xml
<?xml version="1.0"?>
<ruleset name="My Drupal Project">
  <description>Coding standard for My Drupal Project</description>

  <!-- Paths to analyze -->
  <file>web/modules/custom</file>
  <file>web/themes/custom</file>

  <!-- Exclusions -->
  <exclude-pattern>*/vendor/*</exclude-pattern>
  <exclude-pattern>*/core/*</exclude-pattern>
  <exclude-pattern>*/contrib/*</exclude-pattern>

  <!-- NCAC Standard -->
  <rule ref="NCAC"/>

  <!-- ★ CRITICAL: Drupal Options ★ -->
  <rule ref="NCAC.NamingConventions.FunctionName">
    <properties>
      <property name="allowDoubleUnderscore" value="1"/>
      <property name="allowLeadingUnderscore" value="1"/>
    </properties>
  </rule>

  <!-- Drupal Extensions -->
  <arg name="extensions" value="php,module,inc,install,test,profile,theme"/>
  <arg name="colors"/>
  <arg value="sp"/>
</ruleset>
```

### 3. Test on a small module first

Before analyzing the entire project, test on a small module:

```bash
# Analysis without fixing
vendor/bin/phpcs web/modules/custom/my_small_module

# Dry-run (see what would be fixed)
vendor/bin/phpcbf --dry-run web/modules/custom/my_small_module

# Actual fix
vendor/bin/phpcbf web/modules/custom/my_small_module
```

### 4. Verify hooks are preserved

After running `phpcbf`, verify that your hooks are still correct:

```bash
# Search for all preprocess hooks
grep -r "function.*preprocess.*__" web/modules/custom/

# Check theme_suggestions
grep -r "function.*theme_suggestions.*__" web/modules/custom/
```

**Expected**: You should see the double underscores (`__`) intact.

## 🔍 Valid Drupal Code Examples

### Preprocess Hooks

```php
// ✅ Hook for node--homepage.html.twig
function mymodule_preprocess_node__homepage(array &$variables): void {
  $variables['custom_data'] = 'homepage';
}

// ✅ Hook for paragraph--chapitres.html.twig
function mymodule_preprocess_paragraph__chapitres(array &$variables): void {
  $variables['chapter_list'] = [];
}

// ✅ Hook for node--article-avec-sommaire.html.twig
function mymodule_preprocess_node__article_avec_sommaire(array &$variables): void {
  $variables['has_summary'] = true;
}
```

### Theme Suggestions

```php
// ✅ Modify template suggestions for node
function mymodule_theme_suggestions_node__alter(array &$suggestions, array $variables): void {
  $node = $variables['elements']['#node'];
  $suggestions[] = 'node__' . $node->bundle() . '__custom';
}
```

### Internal Functions

```php
// ✅ Private helper function (Drupal convention)
function _mymodule_calculate_price(float $base_price, float $tax_rate): float {
  return $base_price * (1 + $tax_rate);
}

// ✅ Internal function for data processing
function _mymodule_process_items(array $items): array {
  return array_map('strtolower', $items);
}
```

---

## ❓ FAQ

### Q: Is NCAC compatible with Drupal Coding Standard?

**A:** NCAC and Drupal Coding Standard have different philosophies, but NCAC respects essential Drupal conventions (hooks with `__`, etc.). You can use NCAC if you prefer its approach (K&R braces, 2-space indentation).

### Q: What should I do if I already have code with broken hooks?

**A:** If PHPCBF has already transformed `node__homepage` to `node_homepage`, you will need to:

1. Restore from Git: `git checkout -- web/modules/custom/`
2. Configure `allowDoubleUnderscore` in `phpcs.xml.dist`
3. Re-run PHPCBF

### Q: Can I disable these options for specific modules?

**A:** Yes, use `<exclude-pattern>`:

```xml
<rule ref="NCAC.NamingConventions.FunctionName">
  <properties>
    <property name="allowDoubleUnderscore" value="1"/>
  </properties>
  <!-- Don't apply to legacy module -->
  <exclude-pattern>*/modules/custom/legacy_module/*</exclude-pattern>
</rule>
```

### Q: Does this work with Drupal 9, 10, 11?

**A:** Yes! These options are purely syntactic and work with all versions of Drupal that use hooks with double underscores.

---

## 📚 Resources

- **NCAC Documentation**: [README.md](../README.md)
- **Advanced Configuration**: [examples/phpcs.xml.drupal-advanced](../examples/phpcs.xml.drupal-advanced)
- **Drupal Hooks**: https://www.drupal.org/docs/theming-drupal/twig-in-drupal/drupal-twig-filters-and-functions
- **Template Suggestions**: https://www.drupal.org/docs/theming-drupal/twig-in-drupal/working-with-twig-templates

---

## 🆘 Support

If you encounter issues:

1. Verify you're using NCAC **v3.0.2 or higher**
2. Check that `allowDoubleUnderscore` is set to `1` in your `phpcs.xml`
3. Open an issue on GitHub: https://github.com/numediart/NCAC-Coding-Standard/issues

---

**Last Updated**: December 2024  
**Minimum NCAC Version**: 3.0.2
