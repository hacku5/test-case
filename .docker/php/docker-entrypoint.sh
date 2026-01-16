#!/bin/sh
set -e

echo "🚀 Starting setup..."

# Check and install Composer dependencies
echo "📦 Checking Composer dependencies..."
composer install

# Check and install Node dependencies
echo "📦 Checking Node dependencies..."
npm install

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

echo "✅ Setup complete. Starting main process..."
exec "$@"
