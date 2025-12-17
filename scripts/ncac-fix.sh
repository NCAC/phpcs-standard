#!/usr/bin/env bash

###############################################################################
# NCAC Code Quality Workflow
#
# This script runs the complete NCAC quality workflow:
# 1. PHP-CS-Fixer (complex transformations)
# 2. PHPCBF (NCAC-specific corrections)
# 3. PHPCS validation (report remaining issues)
#
# Usage:
#   ./scripts/ncac-fix.sh [path]
#   ./scripts/ncac-fix.sh src/
#   ./scripts/ncac-fix.sh --dry-run src/
###############################################################################

set -euo pipefail

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Default values
DRY_RUN=false
TARGET_PATH="."

# Parse arguments
while [[ $# -gt 0 ]]; do
  case $1 in
    --dry-run)
      DRY_RUN=true
      shift
      ;;
    --help|-h)
      echo "NCAC Code Quality Workflow"
      echo ""
      echo "Usage: $0 [--dry-run] [path]"
      echo ""
      echo "Options:"
      echo "  --dry-run    Preview changes without applying them"
      echo "  --help       Show this help message"
      echo ""
      echo "Examples:"
      echo "  $0 src/              Fix all files in src/"
      echo "  $0 --dry-run src/    Preview changes in src/"
      echo "  $0                   Fix all files in current directory"
      exit 0
      ;;
    *)
      TARGET_PATH="$1"
      shift
      ;;
  esac
done

echo ""
echo -e "${BLUE}╔══════════════════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║ NCAC Code Quality Workflow                                                  ║${NC}"
echo -e "${BLUE}╠══════════════════════════════════════════════════════════════════════════════╣${NC}"
echo -e "${BLUE}║ Target: ${TARGET_PATH}${NC}"
if [ "$DRY_RUN" = true ]; then
  echo -e "${BLUE}║ Mode: DRY RUN (preview only)                                                 ║${NC}"
fi
echo -e "${BLUE}╚══════════════════════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Check if tools are available
if [ ! -f vendor/bin/php-cs-fixer ]; then
  echo -e "${RED}❌ php-cs-fixer not found. Run: composer install${NC}"
  exit 1
fi

if [ ! -f vendor/bin/phpcbf ]; then
  echo -e "${RED}❌ phpcbf not found. Run: composer install${NC}"
  exit 1
fi

# Determine PHP-CS-Fixer config
CONFIG_FILE=""
if [ -f .php-cs-fixer.php ]; then
  CONFIG_FILE=".php-cs-fixer.php"
elif [ -f vendor/ncac/phpcs-standard/.php-cs-fixer.dist.php ]; then
  CONFIG_FILE="vendor/ncac/phpcs-standard/.php-cs-fixer.dist.php"
else
  echo -e "${YELLOW}⚠️  No PHP-CS-Fixer config found, using defaults${NC}"
fi

# Step 1: PHP-CS-Fixer
echo -e "${BLUE}➜ Step 1/3: Applying PHP-CS-Fixer...${NC}"
if [ "$DRY_RUN" = true ]; then
  if [ -n "$CONFIG_FILE" ]; then
    vendor/bin/php-cs-fixer fix "$TARGET_PATH" --config="$CONFIG_FILE" --dry-run --diff || true
  else
    vendor/bin/php-cs-fixer fix "$TARGET_PATH" --dry-run --diff || true
  fi
else
  if [ -n "$CONFIG_FILE" ]; then
    vendor/bin/php-cs-fixer fix "$TARGET_PATH" --config="$CONFIG_FILE" || true
  else
    vendor/bin/php-cs-fixer fix "$TARGET_PATH" || true
  fi
fi
echo -e "${GREEN}✓ PHP-CS-Fixer complete${NC}"
echo ""

# Step 2: PHPCBF
echo -e "${BLUE}➜ Step 2/3: Applying PHPCBF (NCAC-specific)...${NC}"
if [ "$DRY_RUN" = true ]; then
  vendor/bin/phpcs --standard=NCAC --report=diff "$TARGET_PATH" || true
else
  vendor/bin/phpcbf --standard=NCAC "$TARGET_PATH" || true
fi
echo -e "${GREEN}✓ PHPCBF complete${NC}"
echo ""

# Step 3: PHPCS validation
echo -e "${BLUE}➜ Step 3/3: Validating with PHPCS...${NC}"
if vendor/bin/phpcs --standard=NCAC "$TARGET_PATH"; then
  echo ""
  echo -e "${GREEN}╔══════════════════════════════════════════════════════════════════════════════╗${NC}"
  echo -e "${GREEN}║ 🎉 Success! No violations found                                             ║${NC}"
  echo -e "${GREEN}║ Your code is fully compliant with the NCAC standard                         ║${NC}"
  echo -e "${GREEN}╚══════════════════════════════════════════════════════════════════════════════╝${NC}"
  exit 0
else
  echo ""
  echo -e "${YELLOW}╔══════════════════════════════════════════════════════════════════════════════╗${NC}"
  echo -e "${YELLOW}║ ⚠️  Some violations remain                                                   ║${NC}"
  echo -e "${YELLOW}║                                                                              ║${NC}"
  echo -e "${YELLOW}║ The workflow fixed most issues, but some require manual intervention.       ║${NC}"
  echo -e "${YELLOW}║ Please review the violations above and fix them manually.                   ║${NC}"
  echo -e "${YELLOW}╚══════════════════════════════════════════════════════════════════════════════╝${NC}"
  
  if [ "$DRY_RUN" = false ]; then
    echo ""
    echo -e "${BLUE}💡 Tips:${NC}"
    echo "  - Some violations cannot be auto-fixed (e.g., naming conventions)"
    echo "  - Review each violation and apply manual fixes"
    echo "  - Run this script again after manual fixes"
  fi
  
  exit 1
fi
