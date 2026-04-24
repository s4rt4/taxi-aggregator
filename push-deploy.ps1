# ==============================================================
# Push to GitHub + Deploy to Hostinger (Windows PowerShell)
# Usage: .\push-deploy.ps1 "commit message"
# ==============================================================

param(
    [string]$CommitMsg = "Update"
)

$ErrorActionPreference = "Stop"

Write-Host "Step 1/3: Committing and pushing to GitHub..." -ForegroundColor Cyan
git add -A
$hasChanges = (git status --porcelain) -ne $null
if ($hasChanges) {
    git commit -m "$CommitMsg"
    git push
} else {
    Write-Host "No changes to commit." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Step 2/3: Building assets locally..." -ForegroundColor Cyan
npm run build

Write-Host ""
Write-Host "Committing build assets..." -ForegroundColor Cyan
git add -A -f public/build/
$hasBuildChanges = (git status --porcelain) -ne $null
if ($hasBuildChanges) {
    git commit -m "build: update compiled assets"
    git push
}

Write-Host ""
Write-Host "Step 3/3: Deploying to Hostinger..." -ForegroundColor Cyan

$remoteCommands = @"
cd ~/domains/rushxo.com/public_html/dashboard && \
git pull origin main && \
composer install --no-dev --optimize-autoloader --no-interaction && \
php artisan migrate --force && \
php artisan config:clear && \
php artisan route:clear && \
php artisan view:clear && \
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache && \
chmod -R 775 storage bootstrap/cache && \
echo 'Deployed successfully!'
"@

ssh -p 65002 u562106935@82.198.228.45 $remoteCommands

Write-Host ""
Write-Host "Done! Visit: https://dashboard.rushxo.com" -ForegroundColor Green
