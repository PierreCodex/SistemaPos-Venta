<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * Presentación en kardex (solo para lectura/reportes)
 * =====================================================
 *
 * IMPORTANTE: kardex.cantidad, stock_anterior y stock_resultante
 * siguen SIEMPRE en unidad base. No se tocan. El kardex es la
 * fuente de verdad del inventario y debe hablar un solo idioma.
 *
 * Estas columnas son únicamente para poder mostrar "2 cajas" en
 * el reporte en vez de "48", sin que el reporte tenga que
 * adivinar la presentación.
 *
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kardex', function (Blueprint $table) {
            $table->foreignId('presentacion_id')
                  ->nullable()
                  ->after('producto_id')
                  ->constrained('producto_presentaciones')
                  ->nullOnDelete()
                  ->comment('Presentación del movimiento (solo presentación visual)');

            $table->decimal('cantidad_presentacion', 12, 3)->nullable()
                  ->after('cantidad')
                  ->comment('Cantidad expresada en la presentación. cantidad sigue en base.');
        });

        $this->backfill();
    }

    private function backfill(): void
    {
        DB::statement('
            UPDATE kardex k
            INNER JOIN producto_presentaciones pp
                ON pp.producto_id = k.producto_id
               AND pp.es_base = 1
            SET k.presentacion_id      = pp.id,
                k.cantidad_presentacion = k.cantidad
            WHERE k.presentacion_id IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('kardex', function (Blueprint $table) {
            $table->dropForeign(['presentacion_id']);
            $table->dropColumn(['presentacion_id', 'cantidad_presentacion']);
        });
    }
};
