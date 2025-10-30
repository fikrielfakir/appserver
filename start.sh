#!/bin/bash

# Laravel Development Server Startup Script
echo "Starting Laravel application..."

# Run migrations
php artisan migrate --force

# Start Laravel development server on port 5000
php artisan serve --host=0.0.0.0 --port=5000
