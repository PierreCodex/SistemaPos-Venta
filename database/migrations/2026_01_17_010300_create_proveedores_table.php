<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * 🎓 TUTORIAL: Migración de Proveedores
 * =====================================================
 * 
 * Almacena los proveedores que suministran productos.
 * 
 * 💡 NOTA: 
 * - enum() = Crea un campo con valores fijos permitidos
 * - Es como un "SELECT" con opciones predefinidas
 * 
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            
            // 📄 Tipo de documento: DNI, RUC, CE (Carnet Extranjería)
            $table->enum('tipo_documento', ['DNI', 'RUC', 'CE'])->default('RUC')
                  ->comment('Tipo de documento del proveedor');
            
            // 🔢 Número de documento (único para evitar duplicados)
            $table->string('documento', 20)->unique()->comment('Número de documento');
            
            // 👤 Datos del proveedor
            $table->string('nombre', 200)->comment('Nombre o Razón Social');
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('direccion')->nullable();
            
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
