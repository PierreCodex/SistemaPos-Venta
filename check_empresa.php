<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$e = App\Models\Empresa::first();
echo "DATA_START\n";
echo "ID: " . $e->id . "\n";
echo "Razon: " . $e->razon_social . "\n";
echo "RUC: " . $e->ruc . "\n";
echo "Logo: " . ($e->logo ?? 'NULL') . "\n";
echo "DATA_END\n";
