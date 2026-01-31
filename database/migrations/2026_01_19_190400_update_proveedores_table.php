<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * Optimización de Proveedores - Solo índices
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            // Solo agregar índices para optimizar búsquedas
            $table->index('documento', 'idx_proveedores_documento');
            $table->index('nombre', 'idx_proveedores_nombre');
        });
    }

    public function down(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropIndex('idx_proveedores_documento');
            $table->dropIndex('idx_proveedores_nombre');
        });
    }
};
