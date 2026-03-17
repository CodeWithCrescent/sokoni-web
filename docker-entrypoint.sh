#!/bin/bash

echo "Starting application setup..."

# Wait a moment for file system
sleep 2

# Ensure storage and bootstrap cache directories exist and have correct permissions
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Set permissions
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Ensure .env is writable
if [ -f /var/www/html/.env ]; then
    chmod 666 /var/www/html/.env
else
    echo "ERROR: .env file not found!"
    exit 1
fi

# Generate APP_KEY if not set or empty
APP_KEY=$(grep "^APP_KEY=" /var/www/html/.env | cut -d '=' -f2)
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
    echo "Application key generated successfully"
fi

# Ensure database file exists
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "Creating database file..."
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
    chmod 664 /var/www/html/database/database.sqlite
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force 2>&1 || echo "Migration failed or already up to date"

# Clear and optimize caches
echo "Optimizing application..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache

echo "Starting services..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
