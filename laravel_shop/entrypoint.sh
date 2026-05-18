#!/bin/sh

# Skipping DB Wait based on user request
# Ensure symlinks
if [ ! -L public/storage ]; then
    php artisan storage:link
fi

# Cache configuration & routes
php artisan config:cache

php artisan route:cache
php artisan view:cache

# Run migrations (automatically in production)
php artisan migrate --force || echo "Aviso: Las migraciones fallaron pero el servidor continuará iniciando."



# Seed database (idempotent with firstOrCreate)
# php artisan db:seed

# Inject Render's PORT into Nginx config
if [ -n "$PORT" ]; then
    sed -i "s/listen 80;/listen ${PORT};/g" /etc/nginx/conf.d/default.conf
    echo "Nginx configured to listen on port ${PORT}"
fi

# Start the main container process via richarvey image
exec "$@"
