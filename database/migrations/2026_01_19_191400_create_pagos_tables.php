<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * Tablas: pagos_clientes y pagos_proveedores
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        // Pagos de clientes (para ventas a crédito)
        Schema::create('pagos_clientes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('venta_id')
                  ->constrained('ventas');
            
            $table->foreignId('cliente_id')
                  ->constrained('clientes');
            
            $table->decimal('monto', 12, 2);
            
            $table->enum('metodo_pago', [
                'EFECTIVO', 'TARJETA', 'YAPE', 'PLIN', 'TRANSFERENCIA'
            ])->default('EFECTIVO');
            
            $table->string('referencia', 100)->nullable()
                  ->comment('Número de operación');
            
            $table->dateTime('fecha_pago');
            
            $table->foreignId('user_id')
                  ->constrained('users');
            
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('venta_id', 'idx_pago_cliente_venta');
        });

        // Pagos a proveedores (para compras a crédito)
        Schema::create('pagos_proveedores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('compra_id')
                  ->constrained('compras');
            
            $table->decimal('monto', 12, 2);
            
            $table->enum('metodo_pago', [
                'EFECTIVO', 'TRANSFERENCIA', 'CHEQUE', 'OTRO'
            ])->default('EFECTIVO');
            
            $table->string('referencia', 100)->nullable();
            $table->date('fecha_pago');
            
            $table->foreignId('user_id')
                  ->constrained('users');
            
            $table->text('observaciones')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('compra_id', 'idx_pago_proveedor_compra');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_proveedores');
        Schema::dropIfExists('pagos_clientes');
    }
};
