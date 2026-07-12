# Checklist de deploy a producción — SistemaPos-Venta

**Fecha:** 2026-07-12 · Revisión pre-producción hecha con VigilanteIA en mente.

## Antes de subir (ya hecho en este repo ✅)

- [x] Eliminados los scripts peligrosos de la raíz (`deep_cleanup.php` ¡borraba
      tablas!, `check_*.php`, `restore_migration.php`, `plainTextToken`).
- [x] `.htaccess` raíz como red de seguridad: reescribe todo a `public/` y bloquea
      dotfiles, por si el hosting no permite apuntar el docroot a `public/`.
- [x] `.gitignore` endurecido (`storage/framework/`, logs, `.env*`, temporales).
- [x] `.env.production.example` como plantilla del server.
- [x] API VigilanteIA implementada y probada (humo 4/4 OK — ver `API_VIGILANTEIA.md`).

## En el servidor (cada deploy)

1. **DocumentRoot → `public/`** (primera opción). Si el hosting no lo permite, el
   `.htaccess` raíz cubre, pero `public/` sigue siendo lo correcto.
2. Subir el código (git pull / zip SIN `vendor/` ni `node_modules/`).
3. `composer install --no-dev --optimize-autoloader`
4. Crear `.env` desde `.env.production.example` y completar:
   - `APP_ENV=production`, `APP_DEBUG=false`, `LOG_LEVEL=error` (los 3 críticos)
   - `APP_URL` con el dominio real (HTTPS)
   - Credenciales de BD y correo del server
   - `php artisan key:generate` (solo la primera vez)
5. `php artisan migrate --force`
6. `php artisan db:seed --class=RolesAndPermissionsSeeder --force`
   (crea los permisos `api.vigilante.*` y el rol `vigilante`)
7. Cachear config: `php artisan config:cache && php artisan route:cache && php artisan view:cache`
8. Permisos de carpetas: `storage/` y `bootstrap/cache/` escribibles por el web server.

## Para VigilanteIA (una sola vez, después del primer deploy)

1. Crear el usuario dedicado y su token de PRODUCCIÓN (el de desarrollo NO sirve):
   ```php
   // php artisan tinker
   $u = \App\Models\User::firstOrCreate(
       ['email' => 'vigilante@sistema.local'],
       ['name' => 'VigilanteIA', 'password' => bcrypt(bin2hex(random_bytes(16)))]
   );
   $u->syncRoles(['vigilante']);
   $u->createToken('vigilante-ia', ['api.vigilante.ventas', 'api.vigilante.stock'])->plainTextToken;
   ```
2. Copiar ese token (se muestra UNA vez) y pegarlo en el módulo Integración del
   panel VigilanteIA junto con la URL: `https://tudominio.com/api`.
3. Probar desde fuera:
   `curl -H "Authorization: Bearer TOKEN" "https://tudominio.com/api/vigilante/ventas?sku=X&desde=...&hasta=..."`

## Verificación post-deploy (5 min)

- [ ] `https://tudominio.com` carga y el login funciona.
- [ ] `https://tudominio.com/.env` → 403/404 (NUNCA debe mostrar contenido).
- [ ] Una URL rota (ej. `/xyz123`) muestra el 404 bonito, NO un stack trace
      (si muestra stack trace: `APP_DEBUG` sigue en true — corregir YA).
- [ ] El curl de VigilanteIA responde 200 con token y 401 sin token.

## Nota local (desarrollo)

En desarrollo el token de humo se llama `vigilante-ia-smoke` (revocable desde la
gestión de tokens). El `.env` local se queda con `APP_DEBUG=true` — es correcto ahí.
