# Packagist Submission Guide

## 🚀 How to Submit NCAC PHPCS Standard to Packagist

### Prerequisites

- Package must be publicly available on GitHub ✅
- Must have a valid `composer.json` ✅
- Must be tagged with a version (v1.0.0) ✅

### Steps to Submit

1. **Go to Packagist.org**

   - Open https://packagist.org
   - Log in with your GitHub account

2. **Submit Package**

   - Click "Submit" button (top navigation)
   - Enter repository URL: `https://github.com/ncac/phpcs-standard`
   - Click "Check" to validate

3. **Review Package Information**

   - Verify package name: `ncac/phpcs-standard`
   - Verify description and keywords
   - Ensure version 1.0.0 is detected

4. **Complete Submission**
   - Click "Submit" to add to Packagist
   - Package will be available within minutes

### After Submission

1. **Update README badges**

   ```markdown
   [![Latest Stable Version](https://img.shields.io/packagist/v/ncac/phpcs-standard.svg?style=flat-square)](https://packagist.org/packages/ncac/phpcs-standard)
   [![Total Downloads](https://img.shields.io/packagist/dt/ncac/phpcs-standard.svg?style=flat-square)](https://packagist.org/packages/ncac/phpcs-standard)
   ```

2. **Set up Auto-Update Webhook**

   - Go to package page on Packagist
   - Click "Settings"
   - Enable GitHub Service Hook
   - This ensures automatic updates on new releases

3. **Test Installation**
   ```bash
   composer require --dev ncac/phpcs-standard
   ```

### Troubleshooting

**"Repository not found"**

- Ensure repository is public
- Check URL is correct
- Verify you have admin access to the repository

**"Invalid composer.json"**

- Run `composer validate` locally
- Fix any validation errors
- Push changes and retry

**"Package already exists"**

- Package might already be submitted by someone else
- Check if you have access to manage it
- Contact Packagist support if needed

### Verification

Once submitted, verify the package:

- Package appears at: https://packagist.org/packages/ncac/phpcs-standard
- Installation works: `composer require --dev ncac/phpcs-standard`
- Version 1.0.0 is available
- Auto-update webhook is configured

## 📝 Post-Submission Checklist

- [ ] Package submitted to Packagist
- [ ] Installation tested
- [ ] README badges updated with real Packagist URLs
- [ ] Auto-update webhook configured
- [ ] Team notified of package availability
