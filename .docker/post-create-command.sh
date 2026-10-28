cd /workspace

# ensure that this folder is a trusted git repository
git config --global safe.directory '*'

# allow git track file renamed with different case
git config --local core.ignorecase false

# ignore git track filemode
# This is useful in environments like Docker where file permissions may differ
git config --local core.filemode false

# Verify the effective pnpm version against the project manifest.
expected_pnpm_version=$(node -p "require('./package.json').packageManager.match(/^pnpm@([^+]+)/)[1]") || exit 1
actual_pnpm_version=$(pnpm --version) || exit 1
if [ "$actual_pnpm_version" != "$expected_pnpm_version" ]; then
  echo "❌ Incorrect pnpm version : $actual_pnpm_version (expected : $expected_pnpm_version). Rebuild the DevContainer." >&2
  exit 1
fi

# Install dependencies without modifying the lockfile; stop on failure.
pnpm install --frozen-lockfile || exit 1

# composer install and autoload
composer i
composer dump-autoload

# initialize Husky (if not already initialized)
npx husky install

# ensure commit-msg hook is executable
chmod +x .husky/commit-msg 2>/dev/null || true

echo "✅ Post-create command script finished."
# end of script