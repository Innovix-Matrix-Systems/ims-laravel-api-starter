#!/bin/bash

# Go to the project directory
cd /var/www/your-project-path

# Pull the latest changes from the Git repository
echo Pulling latest changes ....
sudo git pull # currently deploying develop branch

# Install/update Composer dependencies
echo Installing composer ....
sudo composer install --no-interaction --prefer-dist --optimize-autoloader

# Run database migrations (if necessary)
echo Running Database Migration ....
sudo php artisan migrate --force

# Clear caches and optimize
echo Optimizing the app ...
sudo php artisan optimize:clear

# Reload your web server (e.g., Nginx or Apache) if necessary
echo Reloading nginx ....
sudo systemctl reload nginx
# sudo systemctl reload apache2

# Optionally, you can trigger any other post-deployment tasks here

echo "Deployment completed"
