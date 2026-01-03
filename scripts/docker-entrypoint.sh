#!/bin/sh

# Docker entrypoint script for Laravel application
set -e

echo "🚀 Starting Laravel application..."

# Wait for database to be ready with timeout
echo "⏳ Waiting for database connection..."

# Use our custom database wait script if available, otherwise fallback to simpler check
if [ -f "/var/www/scripts/wait-for-db.sh" ]; then
    /var/www/scripts/wait-for-db.sh
else
    MAX_ATTEMPTS=30
    ATTEMPT=0

    # Simple database connection test using PHP
    until php -r "
        try {
            \$host = getenv('DB_HOST') ?: 'db';
            \$port = getenv('DB_PORT') ?: '3306';
            \$database = getenv('DB_DATABASE') ?: 'laravel';
            \$username = getenv('DB_USERNAME') ?: 'laravel';
            \$password = getenv('DB_PASSWORD') ?: 'secret';
            \$pdo = new PDO(\"mysql:host=\$host;port=\$port;dbname=\$database\", \$username, \$password);
            \$pdo->query('SELECT 1');
            exit(0);
        } catch (Exception \$e) {
            exit(1);
        }
    " > /dev/null 2>&1; do
        ATTEMPT=$((ATTEMPT + 1))
        if [ $ATTEMPT -ge $MAX_ATTEMPTS ]; then
            echo "❌ Database connection failed after $MAX_ATTEMPTS attempts (60 seconds)"
            echo "Please check your database configuration and ensure the database container is running"
            echo "Database configuration:"
            echo "  Host: ${DB_HOST:-db}"
            echo "  Port: ${DB_PORT:-3306}"
            echo "  Database: ${DB_DATABASE:-laravel}"
            echo "  Username: ${DB_USERNAME:-laravel}"
            exit 1
        fi
        echo "🔄 Database not ready, waiting 2 seconds... (attempt $ATTEMPT/$MAX_ATTEMPTS)"
        sleep 2
    done
    
    echo "✅ Database connection established"
fi

# Link storage
echo "🔧 Linking storage..."
php artisan storage:link

# Run database migrations
echo "🔧 Running database migrations..."
php artisan migrate --force

# Cache configuration and routes for better performance
echo "⚡ Optimizing application..."
php artisan optimize: || echo "⚠️ optimization failed, continuing..."

echo "🎉 Laravel application is ready!"

# Documentation generation
php artisan scribe:generate

# Execute the main command
exec "$@"
