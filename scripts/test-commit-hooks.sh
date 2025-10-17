#!/bin/bash

# Test script for Husky hooks
# Usage: scripts/test-commit-hooks.sh

echo "🧪 Testing commit message validation hooks"
echo ""

# Test messages
declare -a test_messages=(
    "feat: add new feature"
    "fix: correct critical bug"
    "chore: update dependencies"
    "refacto: simplify code"
    "docs: update documentation"
    "release: bump version to 1.2.0"
    "test: invalid message"
    "update: another invalid prefix"
    "feat:no space"
    "FEAT: uppercase prefix"
)

# Expected valid messages (first 6)
valid_count=6

echo "📝 Testing valid messages:"
for i in $(seq 0 $((valid_count-1))); do
    message="${test_messages[$i]}"
    echo -n "  Testing: '$message' ... "
    echo "$message" > /tmp/test-commit-msg
    if ./.husky/commit-msg /tmp/test-commit-msg >/dev/null 2>&1; then
        echo "✅ PASS"
    else
        echo "❌ FAIL (should be valid)"
    fi
done

echo ""
echo "📝 Testing invalid messages:"
for i in $(seq $valid_count $((${#test_messages[@]}-1))); do
    message="${test_messages[$i]}"
    echo -n "  Testing: '$message' ... "
    echo "$message" > /tmp/test-commit-msg
    if ./.husky/commit-msg /tmp/test-commit-msg >/dev/null 2>&1; then
        echo "❌ FAIL (should be invalid)"
    else
        echo "✅ PASS"
    fi
done

echo ""
echo "✅ Tests completed!"

# Cleanup
rm -f /tmp/test-commit-msg
