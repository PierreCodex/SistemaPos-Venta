<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Actualizamos el ENUM para incluir RECIBO y NOTA_ENTRADA
        // Usamos raw porque Laravel no soporta nativamente la modificación de ENUMs en MySQL/MariaDB sin cambios drásticos
        DB::statement("ALTER TABLE compras MODIFY COLUMN tipo_comprobante ENUM('FACTURA', 'BOLETA', 'RECIBO', 'NOTA_ENTRADA', 'GUIA', 'TICKET') NOT NULL");
    }

    public function down(): void
    {
        // Revertimos al estado original (teniendo cuidado si ya hay datos con los nuevos tipos)
        DB::statement("ALTER TABLE compras MODIFY COLUMN tipo_comprobante ENUM('FACTURA', 'BOLETA', 'GUIA', 'TICKET') NOT NULL");
    }
};
