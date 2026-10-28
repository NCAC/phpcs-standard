#!/usr/bin/env bash

###############################################################################
# DEPRECATED - use vendor/bin/ncac-format instead.
#
# This script used to duplicate the ncac-format workflow in bash, but it
# ignored the project's phpcs.xml (it forced --standard=NCAC). It now only
# forwards its arguments to ncac-format and will be removed in the next major
# version.
###############################################################################

set -euo pipefail

echo "⚠️  scripts/ncac-fix.sh is deprecated and will be removed in the next major version." >&2
echo "   Use: vendor/bin/ncac-format [--dry-run | --check] [<path>...]" >&2
echo "" >&2

script_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
exec php "${script_dir}/../bin/ncac-format" "$@"
