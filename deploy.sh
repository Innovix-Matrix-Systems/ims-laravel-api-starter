#!/bin/bash

# Go to the project directory

# Pull the latest changes from the Git repository
echo Pulling latest changes ....
git pull

# Install/update Composer dependencies
echo Installing composer ....
composer install --no-interaction --prefer-dist --optimize-autoloader

# Run database migrations (if necessary)
echo Running Database Migration ....
php artisan migrate --force

# Clear caches and optimize
echo Clear optimizing the app ...
php artisan optimize:clear

# Generate API documentation
echo Generating API documentation ....
php artisan scribe:generate


#optimize the app
echo Optimizing the app ...
php artisan optimize


# Optionally, trigger any other post-deployment tasks here

echo "Deployment script has run successfully"