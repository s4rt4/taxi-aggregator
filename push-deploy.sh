#!/bin/bash
# ==============================================================
# Push to GitHub + Deploy to Hostinger in ONE command
# Usage: bash push-deploy.sh "commit message"
# ==============================================================

set -e

COMMIT_MSG="${1:-Update}"

echo "📤 Step 1/3: Committing and pushing to GitHub..."
git add -A
if ! git diff-index --quiet HEAD; then
    git commit -m "$COMMIT_MSG"
else
    echo "No changes to commit."
fi
git push

echo ""
echo "🏗️  Step 2/3: Building assets locally..."
npm run build

echo ""
echo "📤 Step 2.5/3: Committing build assets..."
git add -A -f public/build/
if ! git diff-index --quiet HEAD; then
    git commit -m "build: update compiled assets"
    git push
fi

echo ""
echo "🚀 Step 3/3: Deploying to Hostinger..."
ssh -p 65002 u562106935@82.198.228.45 << 'EOF'
cd ~/domains/rushxo.com/public_html/dashboard
git pull origin main
composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 775 storage bootstrap/cache
echo ""
echo "✅ Deployed successfully!"
echo "🌐 Visit: https://dashboard.rushxo.com"
EOF
