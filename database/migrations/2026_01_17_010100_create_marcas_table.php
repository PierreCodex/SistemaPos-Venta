<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * 🎓 TUTORIAL: Migración de Marcas
 * =====================================================
 * 
 * Tabla simple para almacenar las marcas de productos.
 * Ejemplo: Samsung, LG, Apple, HP, etc.
 * 
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marcas', function (Blueprint $table) {
            $table->id();
            
            // 🏷️ Código único de la marca (ej: SAM, LG, APP)
            $table->string('codigo', 20)->unique()->comment('Código único de la marca');
            
            $table->string('nombre', 100)->comment('Nombre de la marca');
            $table->text('descripcion')->nullable();
            $table->boolean('estado')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marcas');
    }
};
