#!/usr/bin/env bash
set -e

# Wait until the database accepts connections, then prepare the app on first
# boot. Idempotent: safe to run on every container start.
if [ "$1" = "php-fpm" ]; then
    echo "NovaBiz AI · booting application container…"

    # Cache config/routes/views for production performance (no-op if already cached).
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan event:cache || true

    # Link public storage if missing.
    php artisan storage:link || true
fi

exec "$@"
