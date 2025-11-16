#!/bin/sh
set -e

echo "🚀 Starting Laravel Backend..."

# -----------------------------
# Environment check & PORT setup
# -----------------------------
echo "🔍 Environment check:"
echo "   PORT from Railway: ${PORT:-not set}"
echo "   RAILWAY_PUBLIC_DOMAIN: ${RAILWAY_PUBLIC_DOMAIN:-not set}"
echo "   RAILWAY_PRIVATE_DOMAIN: ${RAILWAY_PRIVATE_DOMAIN:-not set}"

export PORT=${PORT:-8080}
echo "📡 App will listen on port: $PORT"

if ! echo "$PORT" | grep -Eq '^[0-9]+$'; then
  echo "⚠️ Invalid PORT detected: '$PORT', defaulting to 8080"
  export PORT=8080
fi

# -----------------------------
# Generate APP_KEY if missing
# -----------------------------
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force
else
    echo "🔑 APP_KEY already set"
fi

# -----------------------------
# Nginx config
# -----------------------------
echo "🔧 Generating nginx config for port $PORT..."
envsubst '${PORT}' < /etc/nginx/http.d/default.conf.template > /etc/nginx/http.d/default.conf

echo "📄 Generated nginx config (first 10 lines):"
head -10 /etc/nginx/http.d/default.conf

# -----------------------------
# Kill existing processes
# -----------------------------
echo "🔍 Cleaning up existing processes..."
pkill -9 php-fpm 2>/dev/null || echo "   No existing PHP-FPM processes found"
pkill -9 nginx 2>/dev/null || echo "   No existing nginx processes found"

# -----------------------------
# Wait for DB ready
# -----------------------------
echo "⏳ Waiting for database connection..."
until php artisan db:monitor > /dev/null 2>&1; do
    echo "Database is not ready yet, waiting 2 seconds..."
    sleep 2
done
echo "✅ Database is ready!"

# -----------------------------
# Clear cached config
# -----------------------------
php artisan config:clear

# -----------------------------
# Run migrations / reset DB
# -----------------------------
if [ "$RESET_DB" = "true" ]; then
    echo "🗑️ RESET_DB=true detected, resetting database..."
    php artisan migrate:fresh --seed --force
else
    echo "📦 Running normal migrations..."
    php artisan migrate --force --no-interaction
fi

# -----------------------------
# Storage link & optimize
# -----------------------------
echo "🔗 Creating storage link..."
php artisan storage:link || echo "⚠️ Storage link already exists"

echo "⚡ Optimizing application..."
php artisan config:clear
if [ "$APP_DEBUG" = "true" ]; then
    echo "⚠️ Debug mode, skipping config cache"
else
    php artisan config:cache
fi
php artisan route:cache
php artisan view:clear 2>/dev/null || echo "⚠️ No view cache to clear"
php artisan view:cache 2>/dev/null || echo "⚠️ Skipped view cache"

echo "✅ Laravel Backend ready!"

# -----------------------------
# Test nginx & start supervisor
# -----------------------------
nginx -t

echo "🚀 Starting supervisor (nginx + php-fpm)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
