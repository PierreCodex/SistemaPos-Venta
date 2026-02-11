<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::table('migrations')->insert([
        'migration' => '2026_02_11_015525_add_caja_sesion_id_to_venta_credito_pagos_table',
        'batch' => 11
    ]);
    echo "Migration record restored\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
