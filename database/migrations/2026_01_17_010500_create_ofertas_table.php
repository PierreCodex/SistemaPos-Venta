<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * 🎓 TUTORIAL: Migración de Ofertas
 * =====================================================
 * 
 * Almacena las ofertas/promociones de productos.
 * Un producto puede tener múltiples ofertas en diferentes fechas.
 * 
 * 💡 NOTA:
 * - cascadeOnDelete() = Si se elimina el producto, se eliminan sus ofertas
 * 
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ofertas', function (Blueprint $table) {
            $table->id();
            
            // 🔗 Relación con producto
            // cascadeOnDelete() = Si eliminas el producto, se eliminan sus ofertas
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->cascadeOnDelete()
                  ->comment('FK a productos');
            
            // 💰 PRECIOS
            $table->decimal('precio_original', 10, 2)->comment('Precio original sin oferta');
            $table->decimal('precio_oferta', 10, 2)->comment('Precio con descuento');
            
            // 📅 VIGENCIA DE LA OFERTA
            $table->dateTime('fecha_inicio')->comment('Inicio de la oferta');
            $table->dateTime('fecha_fin')->comment('Fin de la oferta');
            
            // ✅ ESTADO
            $table->boolean('estado')->default(true)->comment('1=Activa, 0=Inactiva');
            
            $table->timestamps();
            
            // 🔍 Índice para buscar ofertas activas por fecha
            $table->index(['fecha_inicio', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ofertas');
    }
};
