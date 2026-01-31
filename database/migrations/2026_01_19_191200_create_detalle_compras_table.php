<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * Tabla: detalle_compras
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->id();

            $table->foreignId('compra_id')
                  ->constrained('compras')
                  ->cascadeOnDelete();
            
            $table->foreignId('producto_id')
                  ->constrained('productos');
            
            $table->decimal('cantidad', 12, 3);
            $table->decimal('costo_unitario', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('subtotal', 12, 2);
            
            $table->date('fecha_vencimiento')->nullable()
                  ->comment('Vencimiento del lote');
            $table->string('lote', 50)->nullable();

            // Índice
            $table->index('compra_id', 'idx_det_compra');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_compras');
    }
};
