#!/bin/bash

# Laravel Cloud Deployment Script
# This script runs automatically on every deployment

echo "🚀 Starting deployment process..."

# Run migrations
echo "📦 Running migrations..."
php artisan migrate --force

# Run seeders
echo "🌱 Running seeders..."
php artisan db:seed --class=ChartOfAccountSeeder --force

# Clear config cache
echo "🧹 Clearing config cache..."
php artisan config:clear

# Clear application cache
echo "🧹 Clearing application cache..."
php artisan cache:clear

# Optimize application
echo "⚡ Optimizing application..."
php artisan optimize

echo "✅ Deployment completed successfully!"
