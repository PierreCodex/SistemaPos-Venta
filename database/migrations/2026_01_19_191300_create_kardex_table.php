<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * Tabla: kardex (Historial de movimientos de stock)
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kardex', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->cascadeOnDelete();
            
            $table->enum('tipo_movimiento', [
                'VENTA',
                'COMPRA',
                'DEVOLUCION_CLIENTE',
                'DEVOLUCION_PROVEEDOR',
                'INVENTARIO_INICIAL',
                'AJUSTE_POSITIVO',
                'AJUSTE_NEGATIVO',
                'TRANSFERENCIA',
                'MERMA'
            ]);
            
            // Referencia a la operación
            $table->string('referencia_tipo', 50)->nullable()
                  ->comment('ventas, compras, ajustes_inventario');
            $table->unsignedBigInteger('referencia_id')->nullable();
            
            // Movimiento
            $table->decimal('cantidad', 12, 3)
                  ->comment('+ Entrada / - Salida');
            $table->decimal('costo_unitario', 10, 2)->nullable()
                  ->comment('Para valorización de inventario');
            
            // Stock
            $table->decimal('stock_anterior', 12, 3);
            $table->decimal('stock_resultante', 12, 3);
            
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users');
            
            $table->text('observaciones')->nullable();
            
            $table->timestamp('created_at')->useCurrent();

            // Índices
            $table->index('producto_id', 'idx_kardex_producto');
            $table->index('created_at', 'idx_kardex_fecha');
            $table->index('tipo_movimiento', 'idx_kardex_tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kardex');
    }
};
