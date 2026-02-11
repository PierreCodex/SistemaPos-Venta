<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = ['users', 'horarios', 'horario_user'];
foreach ($tables as $table) {
    try {
        $result = DB::select("SHOW TABLE STATUS WHERE Name = ?", [$table]);
        if ($result) {
            echo "Table: $table, Engine: " . $result[0]->Engine . "\n";
        } else {
            echo "Table: $table not found\n";
        }
    } catch (\Exception $e) {
        echo "Error checking $table: " . $e->getMessage() . "\n";
    }
}
