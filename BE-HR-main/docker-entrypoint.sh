#!/bin/sh
set -e

echo "🚀 Starting Laravel Backend..."

# Debug: Show all environment variables related to PORT
echo "🔍 Environment check:"
echo "   PORT from Railway: ${PORT:-not set}"
echo "   RAILWAY_PUBLIC_DOMAIN: ${RAILWAY_PUBLIC_DOMAIN:-not set}"
echo "   RAILWAY_PRIVATE_DOMAIN: ${RAILWAY_PRIVATE_DOMAIN:-not set}"

# Set default PORT to 8080 (matches railway.toml and EXPOSE)
export PORT=${PORT:-8080}
echo "📡 App will listen on port: $PORT"

# Validate PORT is a valid number
if ! echo "$PORT" | grep -Eq '^[0-9]+$'; then
  echo "⚠️ Invalid PORT detected: '$PORT', defaulting to 8080"
  export PORT=8080
fi

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force
else
    echo "🔑 APP_KEY already set"
fi

# Generate nginx config from template with actual PORT
echo "🔧 Generating nginx config for port $PORT..."
envsubst '${PORT}' < /etc/nginx/http.d/default.conf.template > /etc/nginx/http.d/default.conf

# Verify generated config
echo "📄 Generated nginx config:"
cat /etc/nginx/http.d/default.conf | head -10

# Kill any existing processes to prevent port conflicts
echo "🔍 Cleaning up existing processes..."
pkill -9 php-fpm 2>/dev/null || echo "   No existing PHP-FPM processes found"
pkill -9 nginx 2>/dev/null || echo "   No existing nginx processes found"

# Wait for database to be ready
echo "⏳ Waiting for database connection..."
echo "   Host: ${MYSQLHOST:-${DB_HOST}}"
echo "   Port: ${MYSQLPORT:-${DB_PORT}}"
echo "   Database: ${MYSQLDATABASE:-${DB_DATABASE}}"

# Try db:monitor first (Laravel 11+), fallback to PDO connection
until php artisan db:monitor > /dev/null 2>&1; do
    echo "Database is not ready yet, waiting 2 seconds..."
    sleep 2
done
echo "✅ Database is ready!"

# Clear any cached config that might interfere with migration
php artisan config:clear

# Run migrations WITHOUT cache/locks
echo "📦 Running migrations..."
php artisan migrate --force --no-interaction || {
    echo "⚠️  Migration failed or already up to date"
}

# Run database seeders (only if tables are empty to avoid duplicates)
echo "🌱 Checking if database needs seeding..."
ADMIN_COUNT=$(php artisan tinker --execute="echo \App\Models\Admin::count();" 2>/dev/null | tail -1)
if [ "$ADMIN_COUNT" = "0" ] || [ -z "$ADMIN_COUNT" ]; then
    echo "🌱 Running database seeders..."
    php artisan db:seed --force || echo "⚠️  Seeding failed or already seeded"
else
    echo "✅ Database already has data, skipping seed"
fi

# Create storage link (safe - won't fail if already exists)
echo "🔗 Creating storage link..."
php artisan storage:link || echo "⚠️  Storage link already exists"

# Clear and cache config (safe for production)
echo "⚡ Optimizing application..."
php artisan config:clear
php artisan config:cache
php artisan route:cache

# Skip view:cache if views don't exist
php artisan view:cache 2>/dev/null || echo "⚠️  Skipped view cache (no views found)"

echo "✅ Laravel Backend ready!"

# Fix nginx listen port at runtime (double-check before supervisor starts)
PORT=${PORT:-8080}
echo "🎯 Setting Nginx to listen on port ${PORT}..."
envsubst '${PORT}' < /etc/nginx/http.d/default.conf.template > /etc/nginx/http.d/default.conf

# Test nginx configuration before starting
echo "🧪 Testing nginx configuration..."
nginx -t

echo "🚀 Starting supervisor (nginx + php-fpm)..."
# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf