#!/bin/sh

# Docker entrypoint script for Laravel Queue Worker
set -e

echo "🔄 Starting Laravel Queue Worker..."

# Initial wait for app container to start and handle migrations
echo "⏳ Initial wait for app deployment (60 seconds)..."
sleep 60

# Wait for database connection and migrations with exponential backoff
echo "🔍 Checking database connection and migration status..."
max_attempts=20
attempt=1
wait_time=2

while [ $attempt -le $max_attempts ]; do
  # Check if migrations are complete and database is ready
  if php artisan migrate:status > /dev/null 2>&1; then
    echo "✅ Database connection established and migrations are ready"
    
    # Verify that essential tables exist (like cache table)
    if php artisan migrate:status | grep -q "Ran"; then
      echo "✅ Migrations confirmed as completed"
      break
    else
      echo "⚠️  Database connected but no migrations found, waiting..."
    fi
  else
    echo "🔄 Attempt $attempt/$max_attempts: Database not ready, waiting ${wait_time} seconds..."
    sleep $wait_time
    
    # Exponential backoff: double the wait time for next attempt (max 60 seconds)
    wait_time=$((wait_time * 2))
    if [ $wait_time -gt 60 ]; then
      wait_time=60
    fi
    
    attempt=$((attempt + 1))
  fi
done

if [ $attempt -gt $max_attempts ]; then
  echo "❌ Failed to connect to database or migrations not ready after $max_attempts attempts"
  exit 1
fi

# Clear any cached config (packages already discovered during build)
echo "🧹 Clearing caches for queue worker..."
php artisan optimize:clear || echo "⚠️  Cache clear failed, continuing..."

echo "🎉 Queue Worker is ready!"

# Execute the main command
exec "$@"
