#!/bin/sh
set -e

# Check and install Composer dependencies
if [ ! -d "vendor" ]; then
    echo "📦 Composer dependencies missing. Installing..."
    composer install
fi

# Check and install Node dependencies
if [ ! -d "node_modules" ]; then
    echo "📦 Node dependencies missing. Installing..."
    npm install
fi

# Check if setup has already run
if [ -f "storage/.setup_done" ]; then
    echo "✅ Setup already completed. Skipping initialization..."
else
    echo "🚀 First time setup..."

    # Ensure APP_KEY exists
    if [ -f ".env" ]; then
        if ! grep -q "^APP_KEY=base64:" .env; then
            echo "🔑 Generating application key..."
            php artisan key:generate
        fi
    else
        echo "⚠️ .env file not found! Copying .env.example..."
        cp .env.example .env
        php artisan key:generate
    fi

    # Run Migrations and Seed
    echo "🗄️  Running migrations and seeds..."
    php artisan migrate --seed --force

    # Build Assets
    echo "🎨 Building assets..."
    npm run build

    # Create marker file
    touch storage/.setup_done
fi

echo "✅ Ready. Starting main process..."
exec "$@"
