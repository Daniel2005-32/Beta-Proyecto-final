#!/bin/sh

# Cache configuration & routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (automatically in production)
php artisan migrate --force

# Start the main container process via richarvey image
exec "$@"
