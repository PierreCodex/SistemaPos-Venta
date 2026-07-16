<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * Snapshot de presentación en detalle_ventas
 * =====================================================
 *
 * Guarda en qué presentación se vendió cada línea y con qué
 * factor, por la misma razón por la que ya se guarda
 * precio_original: el histórico no puede depender de datos
 * que cambian.
 *
 * factor_aplicado es lo que permite que una anulación revierta
 * exactamente lo que descontó, aunque el catálogo haya cambiado
 * desde entonces.
 *
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->foreignId('presentacion_id')
                  ->nullable()
                  ->after('producto_id')
                  ->constrained('producto_presentaciones')
                  ->restrictOnDelete()
                  ->comment('Presentación vendida en esta línea');

            $table->decimal('factor_aplicado', 12, 4)->default(1.0000)
                  ->after('cantidad')
                  ->comment('Factor vigente al momento de la venta (congelado)');

            $table->decimal('cantidad_base', 12, 3)->default(0.000)
                  ->after('factor_aplicado')
                  ->comment('cantidad * factor_aplicado = lo descontado del stock');
        });

        $this->backfill();
    }

    /**
     * Hasta hoy toda venta fue en unidad base con factor 1,
     * así que cantidad_base = cantidad. Sin transformación de datos.
     */
    private function backfill(): void
    {
        DB::statement('
            UPDATE detalle_ventas dv
            INNER JOIN producto_presentaciones pp
                ON pp.producto_id = dv.producto_id
               AND pp.es_base = 1
            SET dv.presentacion_id = pp.id,
                dv.factor_aplicado = 1.0000,
                dv.cantidad_base   = dv.cantidad
            WHERE dv.presentacion_id IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->dropForeign(['presentacion_id']);
            $table->dropColumn(['presentacion_id', 'factor_aplicado', 'cantidad_base']);
        });
    }
};
