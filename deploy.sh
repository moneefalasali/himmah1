#!/bin/bash

# Wrapper deploy script for Laravel Cloud — delegates to scripts/auto_deploy.sh when present
set -euo pipefail

echo "🚀 Starting Himmah deploy wrapper..."

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
AUTO_SCRIPT="$SCRIPT_DIR/scripts/auto_deploy.sh"

if [ -f "$AUTO_SCRIPT" ] && [ -x "$AUTO_SCRIPT" ]; then
    echo "🔁 Found scripts/auto_deploy.sh — executing"
    "$AUTO_SCRIPT"
    exit 0
fi

if [ -f "$AUTO_SCRIPT" ]; then
    echo "🔧 Found scripts/auto_deploy.sh but it's not executable — making executable and running"
    chmod +x "$AUTO_SCRIPT"
    "$AUTO_SCRIPT"
    exit 0
fi

echo "⚠️ scripts/auto_deploy.sh not found — falling back to built-in deploy steps"

echo "📦 updating dependencies..."
composer install --optimize-autoloader --no-dev || true

echo "🧹 clearing caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "⚡ caching for production..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "🗄️ running migrations..."
php artisan migrate --force || true

echo "🔐 setting permissions..."
chmod -R 755 storage/ || true
chmod -R 755 bootstrap/cache/ || true

if [ -f "package.json" ]; then
    echo "🎨 building assets..."
    npm ci || true
    npm run build || true
fi

echo "✅ deploy finished (fallback path)."

