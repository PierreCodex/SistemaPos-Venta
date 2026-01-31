<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * =====================================================
 * Actualización de detalle_ventas
 * =====================================================
 * 
 * Solo cambia cantidad a decimal y agrega precio_original
 * (descuento ya existe en la tabla original)
 * 
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        // Cambiar cantidad a decimal
        DB::statement('ALTER TABLE detalle_ventas MODIFY cantidad DECIMAL(12,3) NOT NULL DEFAULT 1.000');

        // Solo agregar precio_original si no existe
        if (!Schema::hasColumn('detalle_ventas', 'precio_original')) {
            Schema::table('detalle_ventas', function (Blueprint $table) {
                $table->decimal('precio_original', 10, 2)->after('precio_unitario')
                      ->comment('Precio sin descuento');
            });
        }

        // Agregar índice si no existe
        try {
            Schema::table('detalle_ventas', function (Blueprint $table) {
                $table->index('producto_id', 'idx_det_venta_producto');
            });
        } catch (\Exception $e) {
            // Índice ya existe
        }
    }

    public function down(): void
    {
        try {
            Schema::table('detalle_ventas', function (Blueprint $table) {
                $table->dropIndex('idx_det_venta_producto');
            });
        } catch (\Exception $e) {}

        if (Schema::hasColumn('detalle_ventas', 'precio_original')) {
            Schema::table('detalle_ventas', function (Blueprint $table) {
                $table->dropColumn('precio_original');
            });
        }

        DB::statement('ALTER TABLE detalle_ventas MODIFY cantidad INT NOT NULL DEFAULT 1');
    }
};
