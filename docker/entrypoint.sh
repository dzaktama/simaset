#!/bin/bash
set -e

echo "Setting directory permissions for storage and bootstrap/cache..."
chmod -R 777 storage bootstrap/cache 2>/dev/null || true

if [ ! -f ".env" ]; then
    if [ -f ".env.docker.example" ]; then
        echo "Creating .env from .env.docker.example..."
        cp .env.docker.example .env
    else
        echo "Creating .env from .env.example..."
        cp .env.example .env
    fi
fi

if [ ! -f "vendor/autoload.php" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction
fi

if [ ! -d "node_modules" ]; then
    echo "Installing NPM dependencies..."
    npm install
fi

if grep -q "^APP_KEY=$" .env; then
    echo "Generating APP_KEY..."
    php artisan key:generate
fi

if [ ! -d "public/build" ]; then
    echo "Building Vite assets..."
    npm run build
fi

echo "Attempting to run migrations safely..."
php artisan migrate --force || echo "Migration skipped or failed. You can run it manually later."

exec "$@"
