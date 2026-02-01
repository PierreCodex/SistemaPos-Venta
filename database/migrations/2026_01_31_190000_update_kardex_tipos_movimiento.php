<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Agregar tipos de movimiento adicionales al kardex
 */
return new class extends Migration
{
    public function up(): void
    {
        // En MySQL, modificar el enum para agregar más valores
        DB::statement("ALTER TABLE kardex MODIFY tipo_movimiento ENUM(
            'VENTA',
            'COMPRA',
            'DEVOLUCION_CLIENTE',
            'DEVOLUCION_PROVEEDOR',
            'INVENTARIO_INICIAL',
            'AJUSTE_POSITIVO',
            'AJUSTE_NEGATIVO',
            'AJUSTE_ENTRADA',
            'AJUSTE_SALIDA',
            'TRANSFERENCIA',
            'MERMA',
            'CANCELACION'
        ) NOT NULL");
    }

    public function down(): void
    {
        // Revertir a los valores originales
        DB::statement("ALTER TABLE kardex MODIFY tipo_movimiento ENUM(
            'VENTA',
            'COMPRA',
            'DEVOLUCION_CLIENTE',
            'DEVOLUCION_PROVEEDOR',
            'INVENTARIO_INICIAL',
            'AJUSTE_POSITIVO',
            'AJUSTE_NEGATIVO',
            'TRANSFERENCIA',
            'MERMA'
        ) NOT NULL");
    }
};
