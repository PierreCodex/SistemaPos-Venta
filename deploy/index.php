<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 * MODIFICADO PARA DONWEB / CPANEL
 */

// Definir la ruta al proyecto Laravel (fuera de public_html)
$laravelPath = __DIR__ . '/../laravel';

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $laravelPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $laravelPath.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once $laravelPath.'/bootstrap/app.php')
    ->handleRequest(Kernel::class);
