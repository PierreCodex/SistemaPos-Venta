<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * 🎓 TUTORIAL: Migración de Unidades de Medida
 * =====================================================
 * 
 * Almacena las unidades de medida de los productos.
 * Ejemplo: Unidad, Caja, Kilogramo, Litro, Metro, etc.
 * 
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unidades', function (Blueprint $table) {
            $table->id();
            
            // 📏 Código corto de la unidad (ej: UND, CJA, KG, LT)
            $table->string('codigo', 10)->unique()->comment('Código corto: UND, CJA, KG');
            
            $table->string('nombre', 50)->comment('Nombre completo: Unidad, Caja, Kilogramo');
            $table->text('descripcion')->nullable();
            $table->boolean('estado')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unidades');
    }
};
