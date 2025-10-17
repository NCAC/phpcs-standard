#!/bin/bash

# Script to update README badges after Packagist submission
# Usage: scripts/update-packagist-badges.sh

set -e

echo "🔄 Updating README badges for Packagist..."

# Check if package exists on Packagist
echo "📡 Checking Packagist availability..."
if curl -s "https://packagist.org/packages/ncac/phpcs-standard.json" | grep -q '"status":"success"'; then
    echo "✅ Package found on Packagist!"
    
    # Update README badges
    echo "📝 Updating README badges..."
    
    # Create temporary file with updated badges
    cat > /tmp/badge-update.md << 'EOF'
# NCAC PHPCS Standard

[![Latest Stable Version](https://img.shields.io/packagist/v/ncac/phpcs-standard.svg?style=flat-square)](https://packagist.org/packages/ncac/phpcs-standard)
[![Total Downloads](https://img.shields.io/packagist/dt/ncac/phpcs-standard.svg?style=flat-square)](https://packagist.org/packages/ncac/phpcs-standard)
[![PHP Version](https://img.shields.io/badge/php-7.4%20|%208.0%20|%208.1%20|%208.2-blue)](https://packagist.org/packages/ncac/phpcs-standard)
EOF

    # Update the README file
    if [ -f README.md ]; then
        # Get content after the badges (from line 4 onwards)
        tail -n +4 README.md > /tmp/readme-rest.md
        
        # Combine new badges with rest of README
        cat /tmp/badge-update.md /tmp/readme-rest.md > README.md
        
        echo "✅ README badges updated!"
        echo ""
        echo "🎉 Packagist integration complete!"
        echo "📦 Package available at: https://packagist.org/packages/ncac/phpcs-standard"
        echo ""
        echo "🧪 Test installation:"
        echo "   composer require --dev ncac/phpcs-standard"
        
        # Clean up
        rm -f /tmp/badge-update.md /tmp/readme-rest.md
    else
        echo "❌ README.md not found"
        exit 1
    fi
else
    echo "❌ Package not yet available on Packagist"
    echo ""
    echo "📋 To submit the package:"
    echo "1. Go to https://packagist.org"
    echo "2. Click 'Submit'"
    echo "3. Enter: https://github.com/ncac/phpcs-standard"
    echo "4. Run this script again after submission"
    echo ""
    echo "📚 See docs/packagist-submission.md for detailed instructions"
    exit 1
fi
