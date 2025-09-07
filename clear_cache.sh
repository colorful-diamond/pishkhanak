#!/bin/bash

# Clear Laravel caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo "Laravel caches cleared successfully!" 