#!/bin/bash
set -e

echo "📦 Instalando dependencias PHP..."
composer install --no-dev --optimize-autoloader

echo "📦 Instalando dependencias Node..."
npm install

echo "🏗️ Compilando assets..."
npm run build

echo "🗃️ Ejecutando migraciones..."
php -d max_execution_time=300 artisan migrate --force

echo "🔑 Ejecutando seeder de permisos..."
php -d max_execution_time=300 artisan db:seed --class=RolesAndPermissionsSeeder --force

echo "🧹 Limpiando cache de permisos..."
php -d max_execution_time=300 artisan permission:cache-reset

echo "🔗 Storage link..."
php artisan storage:link 2>/dev/null || true

echo "⚡ Optimizando..."
php artisan optimize

echo "✅ Deploy completado!"
