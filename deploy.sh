#!/bin/bash
# ============================================================
# RushXO Deployment Script for Hostinger
# Usage: bash deploy.sh [fresh|update]
# ============================================================

set -e

MODE="${1:-update}"
APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "=============================================="
echo "RushXO Deployment Script"
echo "Mode: $MODE"
echo "Directory: $APP_DIR"
echo "=============================================="
echo ""

cd "$APP_DIR"

if [ "$MODE" == "fresh" ]; then
    echo "⚠️  FRESH MODE: will wipe database and reseed"
    read -p "Are you sure? (yes/no): " confirm
    if [ "$confirm" != "yes" ]; then
        echo "Aborted."
        exit 1
    fi
fi

# --- Check PHP version
echo "📦 Checking PHP version..."
php -v | head -1
echo ""

# --- Git pull (for update mode)
if [ "$MODE" == "update" ]; then
    echo "📥 Pulling latest code from Git..."
    git pull origin main
    echo ""
fi

# --- Composer install
echo "📦 Installing Composer dependencies (production)..."
composer install --no-dev --optimize-autoloader --no-interaction
echo ""

# --- .env check
if [ ! -f .env ]; then
    echo "❌ .env file not found!"
    echo "Please copy .env.production.example to .env and fill in the values."
    exit 1
fi

# --- Generate APP_KEY if empty
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force
    echo ""
fi

# --- Storage symlink (manual because exec() is disabled on shared hosting)
STORAGE_LINK="$APP_DIR/public/storage"
STORAGE_TARGET="$APP_DIR/storage/app/public"
if [ ! -L "$STORAGE_LINK" ]; then
    echo "🔗 Creating storage symlink..."
    ln -s "$STORAGE_TARGET" "$STORAGE_LINK"
else
    echo "🔗 Storage symlink exists, skipping."
fi
echo ""

# --- Migrations
if [ "$MODE" == "fresh" ]; then
    echo "🗄️  Running FRESH migrations + seed..."
    php artisan migrate:fresh --seed --force
else
    echo "🗄️  Running migrations..."
    php artisan migrate --force
fi
echo ""

# --- Clear all caches first
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
echo ""

# --- Rebuild caches for production
echo "⚡ Caching config, routes, and views for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
echo ""

# --- Permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache
chmod 640 .env
echo ""

# --- Run tests (optional, skip on production)
# echo "🧪 Running tests..."
# php artisan test
# echo ""

echo "=============================================="
echo "✅ Deployment complete!"
echo "=============================================="
echo ""
echo "Next steps:"
echo "  1. Verify site at: https://dashboard.rushxo.com"
echo "  2. Check logs: tail -f storage/logs/laravel.log"
echo "  3. Test admin login: https://dashboard.rushxo.com/login"
echo ""

if [ "$MODE" == "fresh" ]; then
    echo "🔑 Test accounts (password: password123):"
    echo "  - superadmin@test.com (Super Admin)"
    echo "  - admin@test.com (Admin)"
    echo "  - operator@test.com (Operator)"
    echo "  - passenger@test.com (Passenger)"
    echo ""
    echo "⚠️  Remember to delete test accounts before go-live!"
fi
