<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * Snapshot de presentación en detalle_compras
 * =====================================================
 *
 * Mismo criterio que detalle_ventas. La compra es justamente
 * donde más se usa la presentación grande: al proveedor se le
 * compra por caja, no por unidad.
 *
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_compras', function (Blueprint $table) {
            $table->foreignId('presentacion_id')
                  ->nullable()
                  ->after('producto_id')
                  ->constrained('producto_presentaciones')
                  ->restrictOnDelete()
                  ->comment('Presentación comprada en esta línea');

            $table->decimal('factor_aplicado', 12, 4)->default(1.0000)
                  ->after('cantidad')
                  ->comment('Factor vigente al momento de la compra (congelado)');

            $table->decimal('cantidad_base', 12, 3)->default(0.000)
                  ->after('factor_aplicado')
                  ->comment('cantidad * factor_aplicado = lo ingresado al stock');
        });

        $this->backfill();
    }

    private function backfill(): void
    {
        DB::statement('
            UPDATE detalle_compras dc
            INNER JOIN producto_presentaciones pp
                ON pp.producto_id = dc.producto_id
               AND pp.es_base = 1
            SET dc.presentacion_id = pp.id,
                dc.factor_aplicado = 1.0000,
                dc.cantidad_base   = dc.cantidad
            WHERE dc.presentacion_id IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('detalle_compras', function (Blueprint $table) {
            $table->dropForeign(['presentacion_id']);
            $table->dropColumn(['presentacion_id', 'factor_aplicado', 'cantidad_base']);
        });
    }
};
