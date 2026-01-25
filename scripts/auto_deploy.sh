#!/usr/bin/env bash
set -euo pipefail

echo "🚀 Auto deploy script for Himmah — starting"

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT_DIR"

# Load .env values for DB backup if possible
if [ -f .env ]; then
  DB_DATABASE=$(grep -E '^DB_DATABASE=' .env | cut -d'=' -f2- | tr -d '\r')
  DB_USERNAME=$(grep -E '^DB_USERNAME=' .env | cut -d'=' -f2- | tr -d '\r')
  DB_PASSWORD=$(grep -E '^DB_PASSWORD=' .env | cut -d'=' -f2- | tr -d '\r')
  DB_HOST=$(grep -E '^DB_HOST=' .env | cut -d'=' -f2- | tr -d '\r')
else
  DB_DATABASE=""
  DB_USERNAME=""
  DB_PASSWORD=""
  DB_HOST=""
fi

echo "1) Backup database (if credentials available)"
if [ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ]; then
  BACKUP_FILE="/tmp/db-backup-$(date +%F-%H%M).sql"
  echo " - Dumping $DB_DATABASE -> $BACKUP_FILE"
  if command -v mysqldump >/dev/null 2>&1; then
    mysqldump -h "${DB_HOST:-127.0.0.1}" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_FILE"
    echo " - Backup completed: $BACKUP_FILE"
  else
    echo " - mysqldump not found; skipping DB backup"
  fi
else
  echo " - DB credentials not found in .env; skipping DB backup"
fi

echo "2) Install/update PHP dependencies"
composer install --no-dev --optimize-autoloader

if [ -f package.json ]; then
  echo "3) Build frontend assets"
  if command -v npm >/dev/null 2>&1; then
    npm ci
    npm run build
  else
    echo " - npm not found; skipping frontend build"
  fi
fi

echo "4) Run migrations"
php artisan migrate --force

echo "5) Create storage symlink & clear/cache"
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "6) Ensure permissions"
chmod -R 755 storage/ || true
chmod -R 755 bootstrap/cache/ || true

echo "7) Check ffmpeg availability"
if command -v ffmpeg >/dev/null 2>&1; then
  echo " - ffmpeg found: $(ffmpeg -version | head -n1)"
else
  echo " - ffmpeg not found on PATH"
  if [ "$(id -u)" -eq 0 ] && command -v apt-get >/dev/null 2>&1; then
    echo " - Running apt-get install ffmpeg (requires root)"
    apt-get update && apt-get install -y ffmpeg || echo " - apt install failed"
  else
    echo " - To install ffmpeg, run as root: apt-get update && apt-get install -y ffmpeg"
  fi
fi

echo "8) Restart queue workers"
php artisan queue:restart || true
if command -v supervisorctl >/dev/null 2>&1; then
  echo " - Restarting supervisord-managed workers"
  supervisorctl reread || true
  supervisorctl update || true
  supervisorctl restart all || true
fi

echo "✅ Auto-deploy finished. Monitor storage/logs/laravel.log for processing output."

echo "Tip: run 'php artisan queue:work' in foreground for manual verification." 
