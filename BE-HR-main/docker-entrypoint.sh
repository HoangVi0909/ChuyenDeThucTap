#!/bin/sh
set -e

echo "🚀 Starting Laravel Backend..."

# -----------------------------
# Environment check & PORT setup
# -----------------------------
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
head -10 /etc/nginx/http.d/default.conf

# -----------------------------
# Kill existing processes
# -----------------------------
echo "🔍 Cleaning up existing processes..."
pkill -9 php-fpm 2>/dev/null || echo "   No PHP-FPM found"
pkill -9 nginx 2>/dev/null || echo "   No nginx found"

# -----------------------------
# Wait for DB ready
# -----------------------------
echo "⏳ Waiting for database..."
until php artisan db:monitor > /dev/null 2>&1; do
    echo "Database not ready, waiting 2s..."
    sleep 2
done
echo "✅ Database ready!"

# -----------------------------
# Clear config cache
# -----------------------------
php artisan config:clear

# -----------------------------
# -----------------------------
# Migrate DB (chỉ khi cần)
# -----------------------------
echo "🛠️ Running migrations..."
php artisan migrate --force

# -----------------------------
# Storage link & optimize
# -----------------------------
echo "🔗 Creating storage link..."
php artisan storage:link || echo "⚠️ Storage link already exists"

echo "⚡ Optimizing application..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:clear 2>/dev/null || echo "⚠️ No view cache"
php artisan view:cache 2>/dev/null || echo "⚠️ Skipped view cache"

# -----------------------------
# Test nginx & start supervisor
# -----------------------------
nginx -t
echo "🚀 Starting supervisor (nginx + php-fpm)..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
