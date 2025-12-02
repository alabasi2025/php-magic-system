#!/bin/bash

# Laravel Deployment Script
# This script runs all necessary commands after deployment

echo "========================================="
echo "Laravel Deployment Script"
echo "========================================="

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to run command
run_command() {
    local cmd=$1
    echo -e "${YELLOW}Running: php artisan $cmd${NC}"
    php artisan $cmd
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Success${NC}\n"
    else
        echo -e "${RED}✗ Failed${NC}\n"
    fi
}

# Run vendor:publish commands
echo -e "${YELLOW}=== Publishing Vendor Files ===${NC}"
run_command 'vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --force'
run_command 'vendor:publish --provider="Laravel\Telescope\TelescopeServiceProvider" --force'
run_command 'vendor:publish --provider="Barryvdh\Debugbar\ServiceProvider" --force'
run_command 'vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force'
run_command 'vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --force'

# Run migrations
echo -e "${YELLOW}=== Running Migrations ===${NC}"
run_command 'migrate --force'

# Clear cache
echo -e "${YELLOW}=== Clearing Cache ===${NC}"
run_command 'cache:clear'
run_command 'config:clear'
run_command 'route:clear'
run_command 'view:clear'

echo -e "${GREEN}=========================================${NC}"
echo -e "${GREEN}Deployment Complete!${NC}"
echo -e "${GREEN}=========================================${NC}"
