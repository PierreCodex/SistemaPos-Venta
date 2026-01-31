<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * 🎓 TUTORIAL: Migración de Clientes
 * =====================================================
 * 
 * Almacena los clientes del negocio.
 * Similar a proveedores pero para clientes que compran.
 * 
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            
            // 📄 Tipo de documento
            $table->enum('tipo_documento', ['DNI', 'RUC', 'CE', 'PASAPORTE'])->default('DNI');
            
            // 🔢 Número de documento (único)
            $table->string('numero_documento', 20)->unique()->comment('Número de documento');
            
            // 👤 Datos del cliente
            $table->string('nombre', 200)->comment('Nombre completo o Razón Social');
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('direccion')->nullable();
            
            $table->boolean('estado')->default(true);
            $table->timestamps();
            
            // 🔍 Índice para buscar por nombre
            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
