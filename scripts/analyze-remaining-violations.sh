#!/usr/bin/env bash

###############################################################################
# Analyze Remaining Violations
#
# This script analyzes which violations remain unfixed after the workflow
# (php-cs-fixer + phpcbf) to help prioritize migration to custom fixers.
#
# Usage:
#   ./scripts/analyze-remaining-violations.sh
#
# Output:
#   - Summary of violations by type
#   - Most common unfixed violations
#   - Recommendations for fixer development
###############################################################################

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WORKSPACE_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
TMP_DIR="${WORKSPACE_DIR}/e2e-tests/tmp"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo ""
echo "╔══════════════════════════════════════════════════════════════════════════════╗"
echo "║ NCAC Remaining Violations Analysis                                          ║"
echo "╠══════════════════════════════════════════════════════════════════════════════╣"
echo "║ Analyzing violations that survive php-cs-fixer + phpcbf workflow            ║"
echo "╚══════════════════════════════════════════════════════════════════════════════╝"
echo ""

# Create temp directory
mkdir -p "${TMP_DIR}"

# Find all .bad.inc files
BAD_FILES=$(find "${WORKSPACE_DIR}/tests" -name "*.bad.inc" | sort)
TOTAL_FILES=$(echo "${BAD_FILES}" | wc -l)

echo "Found ${TOTAL_FILES} test files to analyze"
echo ""

# Initialize counters
TOTAL_INITIAL_VIOLATIONS=0
TOTAL_REMAINING_VIOLATIONS=0
declare -A VIOLATION_TYPES

# Process each file
COUNTER=0
for BAD_FILE in ${BAD_FILES}; do
  COUNTER=$((COUNTER + 1))
  TEST_NAME=$(basename "${BAD_FILE}" .bad.inc)
  
  echo -ne "Processing [${COUNTER}/${TOTAL_FILES}]: ${TEST_NAME}...\r"
  
  # Create working copy
  WORKING_FILE="${TMP_DIR}/${TEST_NAME}.working.php"
  cp "${BAD_FILE}" "${WORKING_FILE}"
  
  # Count initial violations
  INITIAL_JSON=$(vendor/bin/phpcs --standard=NCAC --report=json "${BAD_FILE}" 2>/dev/null || true)
  INITIAL_VIOLATIONS=$(echo "${INITIAL_JSON}" | jq -r '.totals.errors + .totals.warnings' 2>/dev/null || echo "0")
  
  # Apply workflow
  vendor/bin/php-cs-fixer fix "${WORKING_FILE}" --config=.php-cs-fixer.dist.php --quiet 2>/dev/null || true
  vendor/bin/phpcbf --standard=NCAC "${WORKING_FILE}" 2>/dev/null || true
  
  # Count remaining violations
  REMAINING_JSON=$(vendor/bin/phpcs --standard=NCAC --report=json "${WORKING_FILE}" 2>/dev/null || true)
  REMAINING_VIOLATIONS=$(echo "${REMAINING_JSON}" | jq -r '.totals.errors + .totals.warnings' 2>/dev/null || echo "0")
  
  # Extract violation types
  if [ "${REMAINING_VIOLATIONS}" -gt 0 ]; then
    MESSAGES=$(echo "${REMAINING_JSON}" | jq -r '.files | to_entries[0].value.messages[] | .source' 2>/dev/null || true)
    while IFS= read -r SOURCE; do
      if [ -n "${SOURCE}" ]; then
        VIOLATION_TYPES["${SOURCE}"]=$((${VIOLATION_TYPES["${SOURCE}"]:-0} + 1))
      fi
    done <<< "${MESSAGES}"
  fi
  
  TOTAL_INITIAL_VIOLATIONS=$((TOTAL_INITIAL_VIOLATIONS + INITIAL_VIOLATIONS))
  TOTAL_REMAINING_VIOLATIONS=$((TOTAL_REMAINING_VIOLATIONS + REMAINING_VIOLATIONS))
  
  # Clean up
  rm -f "${WORKING_FILE}"
done

echo ""
echo ""

# Calculate improvement
FIXED_VIOLATIONS=$((TOTAL_INITIAL_VIOLATIONS - TOTAL_REMAINING_VIOLATIONS))
if [ "${TOTAL_INITIAL_VIOLATIONS}" -gt 0 ]; then
  IMPROVEMENT_RATE=$(awk "BEGIN {printf \"%.1f\", ($FIXED_VIOLATIONS / $TOTAL_INITIAL_VIOLATIONS) * 100}")
else
  IMPROVEMENT_RATE="100.0"
fi

# Print summary
echo "╔══════════════════════════════════════════════════════════════════════════════╗"
echo "║ Overall Statistics                                                           ║"
echo "╠══════════════════════════════════════════════════════════════════════════════╣"
printf "║  Test files analyzed:      %-50s║\n" "${TOTAL_FILES}"
printf "║  Initial violations:       %-50s║\n" "${TOTAL_INITIAL_VIOLATIONS}"
printf "║  Remaining violations:     %-50s║\n" "${TOTAL_REMAINING_VIOLATIONS}"
printf "║  Fixed violations:         %-50s║\n" "${FIXED_VIOLATIONS}"
printf "║  Improvement rate:         %-48s%%║\n" "${IMPROVEMENT_RATE}"
echo "╚══════════════════════════════════════════════════════════════════════════════╝"
echo ""

if [ "${TOTAL_REMAINING_VIOLATIONS}" -gt 0 ]; then
  echo "╔══════════════════════════════════════════════════════════════════════════════╗"
  echo "║ Top Remaining Violations (Priority for Custom Fixers)                       ║"
  echo "╠══════════════════════════════════════════════════════════════════════════════╣"
  
  # Sort violations by count
  for VIOLATION in "${!VIOLATION_TYPES[@]}"; do
    echo "${VIOLATION_TYPES[$VIOLATION]} ${VIOLATION}"
  done | sort -rn | head -10 | while read COUNT SOURCE; do
    printf "║  [%-3d] %-70s║\n" "${COUNT}" "${SOURCE}"
  done
  
  echo "╚══════════════════════════════════════════════════════════════════════════════╝"
  echo ""
  
  echo -e "${YELLOW}💡 Recommendations:${NC}"
  echo ""
  echo "1. Review the top violations listed above"
  echo "2. Prioritize creating custom fixers for the most common ones"
  echo "3. Start with violations that have clear fixing logic"
  echo "4. Refer to docs/WORKFLOW_STRATEGY.md for migration guidelines"
  echo ""
  
  echo -e "${BLUE}📚 Next steps:${NC}"
  echo ""
  echo "  # Study existing PHP-CS-Fixer fixers"
  echo "  \$BROWSER https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/tree/master/src/Fixer"
  echo ""
  echo "  # Create your first custom fixer"
  echo "  mkdir -p NCAC/Fixer/Whitespace"
  echo "  # Edit NCAC/Fixer/Whitespace/YourFixer.php"
  echo ""
  echo "  # Add tests"
  echo "  mkdir -p tests/Fixer/Whitespace"
  echo "  # Edit tests/Fixer/Whitespace/YourFixerTest.php"
  echo ""
else
  echo -e "${GREEN}🎉 Perfect! No remaining violations!${NC}"
  echo ""
  echo "The workflow (php-cs-fixer + phpcbf) fixes 100% of violations."
  echo "All test files achieve perfect conformity with the NCAC standard."
  echo ""
fi

# Cleanup
rm -rf "${TMP_DIR}"

echo ""
echo "Analysis complete!"
echo ""
