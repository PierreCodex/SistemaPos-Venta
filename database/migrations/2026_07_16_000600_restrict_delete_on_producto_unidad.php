<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * productos.unidad_id: nullOnDelete -> restrictOnDelete
 * =====================================================
 *
 * La unidad de un producto es su unidad BASE: el idioma en el que está
 * expresado su stock. Con nullOnDelete, borrar una unidad por fuera del
 * servicio (tinker, un seeder, phpMyAdmin) ponía unidad_id = NULL en
 * todos sus productos en silencio, y el stock quedaba sin unidad que lo
 * interprete.
 *
 * UnidadService::eliminar ya lo impedía, pero esa guarda solo protege a
 * quien pasa por el servicio. Esto lo hace cumplir la base de datos.
 *
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['unidad_id']);

            $table->foreign('unidad_id')
                  ->references('id')
                  ->on('unidades')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['unidad_id']);

            $table->foreign('unidad_id')
                  ->references('id')
                  ->on('unidades')
                  ->nullOnDelete();
        });
    }
};
