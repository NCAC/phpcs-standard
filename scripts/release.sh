#!/bin/bash

# Release helper script for NCAC PHPCS Standard
# Usage: scripts/release.sh [patch|minor|major|--dry-run]

set -e

echo "🚀 NCAC PHPCS Standard Release Helper"
echo ""

# Check if we're in a clean git state
if [[ -n $(git status --porcelain) ]]; then
    echo "⚠️  Warning: You have uncommitted changes!"
    echo "Please commit or stash your changes before releasing."
    echo ""
    git status --short
    exit 1
fi

# Check if we're on main branch
current_branch=$(git rev-parse --abbrev-ref HEAD)
if [[ "$current_branch" != "main" ]]; then
    echo "⚠️  Warning: You're not on the main branch!"
    echo "Current branch: $current_branch"
    echo "Please switch to main branch before releasing."
    exit 1
fi

# Pull latest changes
echo "📥 Pulling latest changes from origin..."
git pull origin main

# Run quality checks
echo "🧪 Running quality checks..."
if ! vendor/bin/phing check; then
    echo "❌ Quality checks failed! Please fix the issues before releasing."
    exit 1
fi

echo "✅ Quality checks passed!"
echo ""

# Determine release type
case ${1:-""} in
    --dry-run)
        echo "🔍 Running dry-run mode..."
        npx release-it --dry-run --no-git.requireCleanWorkingDir
        ;;
    patch|minor|major)
        echo "📦 Creating $1 release..."
        npx release-it $1
        ;;
    "")
        echo "📦 Creating interactive release..."
        npx release-it
        ;;
    *)
        echo "❌ Invalid argument: $1"
        echo ""
        echo "Usage: $0 [patch|minor|major|--dry-run]"
        echo ""
        echo "Examples:"
        echo "  $0                # Interactive release"
        echo "  $0 patch          # Patch release (1.0.0 -> 1.0.1)"
        echo "  $0 minor          # Minor release (1.0.0 -> 1.1.0)"
        echo "  $0 major          # Major release (1.0.0 -> 2.0.0)"
        echo "  $0 --dry-run      # Test without making changes"
        exit 1
        ;;
esac

echo ""
echo "🎉 Release process completed!"
