#!/usr/bin/env bash
# Builds deploy/advisory-site.zip for upload to the cPanel hosting (see DEPLOY.md):
# the committed project plus production vendor/, compiled assets and Filament assets.
# No .env, database, logs, uploads, tests or node_modules go into the package.
#
# Usage: bash scripts/package-for-cpanel.sh

set -euo pipefail

root="$(cd "$(dirname "$0")/.." && pwd)"
build="$root/deploy/build"
zip="$root/deploy/advisory-site.zip"

cd "$root"

echo "→ Compiling front-end assets"
npm run build

echo "→ Publishing Filament assets"
php artisan filament:assets --quiet

echo "→ Copying project files"
rm -rf "$build" "$zip"
mkdir -p "$build"
git ls-files --cached --others --exclude-standard \
    | grep -vE '^(tests/|deploy/|scripts/|\.claude/|\.github/|node_modules/|phpunit\.xml|CLAUDE\.md|AGENTS\.md|\.env\.example$)' \
    | while IFS= read -r file; do
        [ -f "$file" ] || continue
        mkdir -p "$build/$(dirname "$file")"
        cp "$file" "$build/$file"
    done

# Git-ignored build output the server needs.
cp -r public/build "$build/public/build"
mkdir -p "$build/public/css" "$build/public/js"
cp -r public/css/filament "$build/public/css/filament"
cp -r public/js/filament "$build/public/js/filament"

echo "→ Installing production PHP dependencies"
# The server has the intl extension; this machine may not, so only that check is skipped.
(cd "$build" && composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts --ignore-platform-req=ext-intl)
(cd "$build" && composer dump-autoload --optimize --no-dev --no-scripts --quiet)

# Empty runtime directories Laravel expects to exist.
for dir in storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache; do
    mkdir -p "$build/$dir"
done

echo "→ Zipping"
python - "$build" "$zip" <<'PY'
import os, sys, zipfile
build, target = sys.argv[1], sys.argv[2]
with zipfile.ZipFile(target, "w", zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
    for folder, dirs, files in os.walk(build):
        rel = os.path.relpath(folder, build)
        if not files and not dirs:
            archive.write(folder, rel + "/")
        for name in files:
            path = os.path.join(folder, name)
            archive.write(path, os.path.relpath(path, build))
PY

rm -rf "$build"
echo "✓ $(du -h "$zip" | cut -f1) → deploy/advisory-site.zip"
