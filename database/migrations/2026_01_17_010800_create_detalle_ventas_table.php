<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * 🎓 TUTORIAL: Migración de Detalle de Ventas
 * =====================================================
 * 
 * Almacena cada producto vendido en una venta.
 * Relación: Una VENTA tiene muchos DETALLES
 * 
 * 💡 EJEMPLO:
 * Venta #1:
 *   - Detalle 1: Laptop HP x 1 = S/ 2500
 *   - Detalle 2: Mouse x 2 = S/ 360
 *   - Detalle 3: Teclado x 1 = S/ 250
 * 
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            
            // 🔗 RELACIONES
            // Venta a la que pertenece este detalle
            $table->foreignId('venta_id')
                  ->constrained('ventas')
                  ->cascadeOnDelete() // Si se elimina la venta, se eliminan sus detalles
                  ->comment('FK a ventas');
            
            // Producto vendido
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->comment('FK a productos');
            
            // 📦 CANTIDAD Y PRECIOS
            $table->integer('cantidad')->default(1)->comment('Cantidad vendida');
            $table->decimal('precio_unitario', 10, 2)->comment('Precio al momento de la venta');
            $table->decimal('descuento', 10, 2)->default(0)->comment('Descuento aplicado');
            $table->decimal('subtotal', 12, 2)->comment('(cantidad * precio) - descuento');
            
            $table->timestamps();
            
            // 🔍 Índice para reportes de productos más vendidos
            $table->index('producto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
