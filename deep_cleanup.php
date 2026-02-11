<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    Schema::disableForeignKeyConstraints();
    Schema::dropIfExists('asistencias');
    Schema::dropIfExists('horario_user');
    Schema::dropIfExists('horarios');
    
    DB::table('migrations')->where('migration', 'like', '%2026_02_11%')->delete();
    
    Schema::enableForeignKeyConstraints();
    echo "Cleanup complete\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
