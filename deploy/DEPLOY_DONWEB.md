# ============================================================
# GUÍA DE DEPLOY EN DONWEB (cPanel)
# ============================================================

## PASO 1: CREAR BASE DE DATOS EN DONWEB

1. Ingresa a cPanel → "Bases de datos MySQL"
2. Crea una nueva base de datos (ej: tuusuario_master)
3. Crea un usuario MySQL con contraseña segura
4. Asigna TODOS los privilegios al usuario sobre la base de datos
5. Anota los datos:
   - DB_DATABASE: tuusuario_master
   - DB_USERNAME: tuusuario_dbuser
   - DB_PASSWORD: tu_contraseña_segura
   - DB_HOST: localhost


## PASO 2: SUBIR ARCHIVOS

### Opción A: Usando Administrador de Archivos (cPanel)

1. Comprime tu proyecto en un ZIP (sin la carpeta `vendor`)
2. Sube el ZIP a /home/tuusuario/ (NO en public_html)
3. Descomprime y renombra la carpeta a "laravel"
4. Copia SOLO el contenido de /laravel/public/ a /public_html/

### Opción B: Usando FTP (FileZilla)

1. Conecta con los datos de FTP de DonWeb
2. Sube todo el proyecto (excepto vendor) a /laravel/
3. Sube el contenido de /public/ a /public_html/


## PASO 3: CONFIGURAR EL ARCHIVO .ENV

1. Edita /home/tuusuario/laravel/.env con estos valores:

```env
APP_NAME="Tu Sistema"
APP_ENV=production
APP_KEY=base64:GENERA_UNA_NUEVA_KEY
APP_DEBUG=false
APP_URL=https://tudominio.com

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tuusuario_master
DB_USERNAME=tuusuario_dbuser
DB_PASSWORD=tu_contraseña

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```


## PASO 4: MODIFICAR index.php EN PUBLIC_HTML

Reemplaza el contenido de /public_html/index.php con:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Ruta al proyecto Laravel (fuera de public_html)
$laravelPath = dirname(__DIR__) . '/laravel';

// Maintenance mode
if (file_exists($maintenance = $laravelPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoloader
require $laravelPath.'/vendor/autoload.php';

// Bootstrap
$app = require_once $laravelPath.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
```


## PASO 5: INSTALAR DEPENDENCIAS (SSH o Terminal cPanel)

Si tienes acceso SSH:
```bash
cd ~/laravel
composer install --optimize-autoloader --no-dev
php artisan key:generate
php artisan storage:link
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Si NO tienes SSH, sube la carpeta `vendor` completa desde tu PC.


## PASO 6: PERMISOS DE CARPETAS

Asegúrate de que estas carpetas tengan permisos 775:
- /laravel/storage/
- /laravel/bootstrap/cache/

En cPanel → Administrador de Archivos, click derecho → Permisos → 775


## PASO 7: CREAR ENLACE SIMBÓLICO STORAGE

Si tienes SSH:
```bash
cd ~/public_html
ln -s ../laravel/storage/app/public storage
```

Si NO tienes SSH:
1. Crea manualmente la carpeta /public_html/storage/
2. Sube los archivos de /laravel/storage/app/public/ ahí


## PASO 8: IMPORTAR BASE DE DATOS

1. Exporta tu base de datos local desde phpMyAdmin (SQL)
2. En DonWeb cPanel → phpMyAdmin
3. Selecciona tu base de datos
4. Importa el archivo .sql


## SOLUCIÓN DE PROBLEMAS COMUNES

### Error 500
- Verifica permisos de storage/ y bootstrap/cache/ (775)
- Revisa el archivo .env
- Mira los logs en /laravel/storage/logs/

### Página en blanco
- APP_DEBUG=true temporalmente para ver errores
- Verifica que index.php apunte correctamente

### Assets no cargan (CSS/JS)
- Verifica el APP_URL en .env
- Revisa que /public_html/build/ exista

### Storage no funciona
- Crea el symlink o copia manual los archivos


## ESTRUCTURA FINAL

```
/home/tuusuario/
├── laravel/                    ← Proyecto Laravel
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env                    ← Configuración de producción
│   └── ...
│
└── public_html/                ← Documento raíz (dominio)
    ├── build/                  ← Assets compilados
    ├── storage/                ← Symlink a laravel/storage/app/public
    ├── .htaccess
    ├── index.php               ← Modificado para apuntar a /laravel
    ├── favicon.ico
    └── robots.txt
```


## CHECKLIST FINAL

[ ] Base de datos creada y configurada
[ ] Archivos subidos correctamente
[ ] .env configurado para producción
[ ] index.php modificado
[ ] Vendor instalado/subido
[ ] Permisos de carpetas configurados
[ ] Storage enlazado
[ ] Migraciones ejecutadas
[ ] APP_DEBUG=false en producción
