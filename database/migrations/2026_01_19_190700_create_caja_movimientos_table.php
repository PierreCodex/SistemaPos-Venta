<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * Tabla: caja_movimientos (Ingresos/Egresos de Caja)
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja_movimientos', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('caja_sesion_id')
                  ->constrained('caja_sesiones')
                  ->cascadeOnDelete();
            
            $table->enum('tipo', ['INGRESO', 'EGRESO']);
            
            $table->enum('concepto', [
                'VENTA', 
                'COMPRA', 
                'PAGO_PROVEEDOR', 
                'GASTO', 
                'DEPOSITO', 
                'RETIRO', 
                'PAGO_CLIENTE',
                'OTRO'
            ])->default('OTRO');
            
            $table->decimal('monto', 12, 2);
            $table->string('descripcion', 255);
            
            // Referencia a la operación que generó el movimiento
            $table->string('referencia_tipo', 50)->nullable()
                  ->comment('ventas, compras, gastos, etc.');
            $table->unsignedBigInteger('referencia_id')->nullable();
            
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->comment('Usuario que registró');
            
            $table->timestamp('created_at')->useCurrent();

            // Índice
            $table->index('caja_sesion_id', 'idx_mov_caja_sesion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_movimientos');
    }
};
