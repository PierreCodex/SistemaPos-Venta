<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * Tabla: compras (Ingreso de mercadería)
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();

            $table->foreignId('proveedor_id')
                  ->constrained('proveedores');
            
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->comment('Usuario que registra');
            
            $table->enum('tipo_comprobante', ['FACTURA', 'BOLETA', 'GUIA', 'TICKET']);
            $table->string('numero_comprobante', 50);
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable()
                  ->comment('Para crédito del proveedor');
            
            // Montos
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('igv', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            
            // Pago
            $table->enum('forma_pago', ['CONTADO', 'CREDITO'])->default('CONTADO');
            $table->enum('estado_pago', ['PENDIENTE', 'PARCIAL', 'PAGADO'])->default('PAGADO');
            $table->decimal('monto_pagado', 12, 2)->default(0);
            
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['PENDIENTE', 'COMPLETADO', 'ANULADO'])->default('COMPLETADO');
            
            $table->timestamps();

            // Índices
            $table->index('fecha_emision', 'idx_compras_fecha');
            $table->index('proveedor_id', 'idx_compras_proveedor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
