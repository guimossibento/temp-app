#!/bin/bash
set -e

echo "Running Composer install..."
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "Installing Node modules..."
npm install

echo "Building assets..."
npm run build

echo "Running migrations..."
php artisan migrate --force

echo "Starting Supervisor..."
exec supervisord -n
