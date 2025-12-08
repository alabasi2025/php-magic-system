#!/bin/bash

# تشغيل migration على Laravel Cloud
echo "Running migrations..."
php artisan migrate --force

echo "Done!"
