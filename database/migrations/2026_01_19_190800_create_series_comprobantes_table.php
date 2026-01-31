<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * =====================================================
 * Tabla: series_comprobantes (Control de Correlativos)
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('series_comprobantes', function (Blueprint $table) {
            $table->id();
            
            $table->enum('tipo', [
                'BOLETA', 
                'FACTURA', 
                'TICKET', 
                'NOTA_CREDITO', 
                'NOTA_DEBITO',
                'GUIA'
            ]);
            
            $table->string('serie', 10);
            $table->integer('correlativo_actual')->default(0);
            $table->integer('correlativo_inicial')->default(1);
            $table->boolean('activo')->default(true);
            
            $table->timestamps();

            $table->unique(['tipo', 'serie'], 'series_tipo_serie_unique');
        });

        // Datos semilla
        DB::table('series_comprobantes')->insert([
            ['tipo' => 'BOLETA', 'serie' => 'B001', 'correlativo_actual' => 0, 'created_at' => now()],
            ['tipo' => 'FACTURA', 'serie' => 'F001', 'correlativo_actual' => 0, 'created_at' => now()],
            ['tipo' => 'TICKET', 'serie' => 'T001', 'correlativo_actual' => 0, 'created_at' => now()],
            ['tipo' => 'NOTA_CREDITO', 'serie' => 'BC01', 'correlativo_actual' => 0, 'created_at' => now()],
            ['tipo' => 'NOTA_CREDITO', 'serie' => 'FC01', 'correlativo_actual' => 0, 'created_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('series_comprobantes');
    }
};
