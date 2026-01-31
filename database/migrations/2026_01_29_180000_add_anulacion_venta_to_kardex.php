<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Actualiza el ENUM de tipo_movimiento para incluir ANULACION_VENTA
 */
return new class extends Migration
{
    public function up(): void
    {
        // Modificar el ENUM para agregar el nuevo tipo
        DB::statement("ALTER TABLE kardex MODIFY COLUMN tipo_movimiento ENUM(
            'VENTA',
            'COMPRA',
            'DEVOLUCION_CLIENTE',
            'DEVOLUCION_PROVEEDOR',
            'INVENTARIO_INICIAL',
            'AJUSTE_POSITIVO',
            'AJUSTE_NEGATIVO',
            'TRANSFERENCIA',
            'MERMA',
            'ANULACION_VENTA'
        )");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE kardex MODIFY COLUMN tipo_movimiento ENUM(
            'VENTA',
            'COMPRA',
            'DEVOLUCION_CLIENTE',
            'DEVOLUCION_PROVEEDOR',
            'INVENTARIO_INICIAL',
            'AJUSTE_POSITIVO',
            'AJUSTE_NEGATIVO',
            'TRANSFERENCIA',
            'MERMA'
        )");
    }
};
