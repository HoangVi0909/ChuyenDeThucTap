#!/bin/sh
set -e

echo "🚀 Starting Laravel Frontend..."

# Ensure storage directories exist and have correct permissions
echo "📂 Setting up storage directories..."
mkdir -p /var/www/html/storage/logs \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/bootstrap/cache

# Fix permissions for www-data user (PHP-FPM runs as www-data)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Debug: Show all environment variables related to PORT
echo "🔍 Environment check:"
echo "   PORT from Railway: ${PORT:-not set}"
echo "   RAILWAY_PUBLIC_DOMAIN: ${RAILWAY_PUBLIC_DOMAIN:-not set}"

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

# Force set SESSION_DRIVER and CACHE_STORE if not provided
export SESSION_DRIVER=${SESSION_DRIVER:-database}
export CACHE_STORE=${CACHE_STORE:-database}

# Debug: Show important Laravel config
echo "🔍 Laravel Configuration:"
echo "   APP_ENV: ${APP_ENV:-not set}"
echo "   APP_DEBUG: ${APP_DEBUG:-not set}"
echo "   SESSION_DRIVER: ${SESSION_DRIVER}"
echo "   CACHE_STORE: ${CACHE_STORE}"
echo "   DB_CONNECTION: ${DB_CONNECTION:-not set}"
echo "   DB_HOST: ${DB_HOST:-not set}"
echo "   BACKEND_API_URL: ${BACKEND_API_URL:-not set}"

# Run migrations for cache and session tables (nếu dùng database cache/session)
if [ "$CACHE_STORE" = "database" ] || [ "$SESSION_DRIVER" = "database" ]; then
    echo "🗄️  Running cache/session migrations..."
    php artisan cache:table 2>/dev/null || echo "⚠️  Cache table migration failed or already exists"
    php artisan session:table 2>/dev/null || echo "⚠️  Session table migration failed or already exists"
   php artisan migrate --force 2>/dev/null || echo "⚠️  Migrations already up to date"
fi
# if [ "$CACHE_STORE" = "database" ] || [ "$SESSION_DRIVER" = "database" ]; then
#     echo "🗄️  Running cache/session migrations..."
#     php artisan cache:table 2>/dev/null || echo "⚠️  Cache table migration failed or already exists"
#     php artisan session:table 2>/dev/null || echo "⚠️  Session table migration failed or already exists"

#     # Thay migrate bình thường bằng migrate:fresh + seed
#     echo "🔄 Resetting database and running seeders..."
#     php artisan migrate:fresh --seed --force
# fi

# Create storage link (safe - won't fail if already exists)
echo "🔗 Creating storage link..."
php artisan storage:link 2>/dev/null || echo "⚠️  Storage link already exists"

# Clear and cache config (safe for production)
echo "⚡ Optimizing application..."
php artisan config:clear

# DON'T cache config in debug mode - we need to see errors
if [ "$APP_DEBUG" = "true" ]; then
    echo "⚠️  Debug mode: skipping config cache to see errors"
else
    php artisan config:cache
fi

php artisan route:cache

# Clear view cache first to avoid issues
php artisan view:clear 2>/dev/null || echo "⚠️  No view cache to clear"

# Skip view:cache if views don't exist
php artisan view:cache 2>/dev/null || echo "⚠️  Skipped view cache (no views found)"

# Show storage permissions and existing logs
echo "📂 Storage permissions:"
ls -la /var/www/html/storage/logs 2>/dev/null || echo "⚠️  Logs directory not found"
touch /var/www/html/storage/logs/laravel.log 2>/dev/null || echo "⚠️  Cannot create log file"

# Show existing Laravel errors if any
if [ -f /var/www/html/storage/logs/laravel.log ]; then
    LOG_SIZE=$(wc -l < /var/www/html/storage/logs/laravel.log)
    if [ "$LOG_SIZE" -gt 0 ]; then
        echo "⚠️  Found existing errors in laravel.log (last 20 lines):"
        tail -n 20 /var/www/html/storage/logs/laravel.log
    fi
fi

echo "✅ Laravel Frontend ready!"

# Test nginx configuration before starting
echo "🧪 Testing nginx configuration..."
nginx -t

echo "🚀 Starting supervisor (nginx + php-fpm)..."

# Start supervisor in background để có thể tail logs
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf &

# Đợi một chút để Laravel khởi động
sleep 2

# Tail Laravel logs để debug lỗi 500
echo "📋 Tailing Laravel logs (last 50 lines)..."
if [ -f /var/www/html/storage/logs/laravel.log ]; then
    tail -n 50 /var/www/html/storage/logs/laravel.log
    echo "👀 Following new log entries..."
    tail -f /var/www/html/storage/logs/laravel.log &
else
    echo "⚠️  No Laravel log file found yet"
fi

# Wait for supervisor
wait
