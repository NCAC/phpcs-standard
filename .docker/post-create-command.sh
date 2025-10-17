cd /workspace

# ensure that this folder is a trusted git repository
git config --global safe.directory '*'

# allow git track file renamed with different case
git config --local core.ignorecase false

# ignore git track filemode
# This is useful in environments like Docker where file permissions may differ
git config --local core.filemode false

# install  all the nodejs packages
pnpm i

# composer install and autoload
composer i
composer dump-autoload

# initialize Husky (if not already initialized)
npx husky install

# ensure commit-msg hook is executable
chmod +x .husky/commit-msg 2>/dev/null || true

echo "✅ Post-create command script finished."
# end of script