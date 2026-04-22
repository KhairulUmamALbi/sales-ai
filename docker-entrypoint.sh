#!/bin/bash
set -e

echo "🚀 Starting SalesAI container..."

# Ensure writable directories (in case volume is mounted)
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

# Generate APP_KEY if missing (idempotent — only on first boot)
if ! grep -q "^APP_KEY=base64:" /var/www/.env 2>/dev/null; then
    echo "📝 Generating APP_KEY..."
    php artisan key:generate --force
fi

# Wait for DB to be ready (useful when db is a container that starts alongside)
echo "⏳ Waiting for database connection..."
max_tries=30
tries=0
until php artisan db:show --no-interaction > /dev/null 2>&1; do
    tries=$((tries + 1))
    if [ $tries -ge $max_tries ]; then
        echo "⚠️  Database not reachable after $max_tries attempts — proceeding anyway (app will error on first request if DB is down)"
        break
    fi
    echo "   ...waiting ($tries/$max_tries)"
    sleep 2
done

# Run migrations
echo "📦 Running migrations..."
php artisan migrate --force || echo "⚠️  Migration failed — check DB config"

# Cache optimization for production
echo "⚡ Optimizing Laravel caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage symlink (safe to re-run)
php artisan storage:link --force 2>/dev/null || true


echo "✅ Ready! Starting FrankenPHP..."

# Execute the CMD
exec "$@"
