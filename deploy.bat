@echo off
echo ==========================================
echo   AI Astro Platform - Deployment Helper
echo ==========================================

echo [1/5] Clearing Caches...
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

echo [2/5] Running Migrations...
echo WARNING: This will run standard migrations. If you need a fresh reset, run 'php artisan migrate:fresh' manually.
php artisan migrate --force

echo [3/5] Optimizing for Production...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo [4/5] Syncing Permissions...
php artisan db:seed --class=FinancePermissionSeeder --force

echo [5/5] Deployment Complete!
echo.
echo REMINDER:
echo 1. Ensure Queue Worker is running: 'php artisan queue:work --tries=3'
echo 2. Deploy Cloud Functions: 'cd functions && firebase deploy --only functions'
echo.
pause
