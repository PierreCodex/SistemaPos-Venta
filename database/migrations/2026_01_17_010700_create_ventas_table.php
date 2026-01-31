<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * 🎓 TUTORIAL: Migración de Ventas (Cabecera)
 * =====================================================
 * 
 * Esta tabla almacena la CABECERA de cada venta.
 * Los productos vendidos van en la tabla detalle_ventas.
 * 
 * 💡 CONCEPTOS:
 * - Una VENTA tiene muchos DETALLES (relación 1:N)
 * - La venta guarda: cliente, totales, comprobante
 * - El detalle guarda: producto, cantidad, precio
 * 
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            
            // 🔗 RELACIONES
            // Cliente (puede ser NULL para venta a público general)
            $table->foreignId('cliente_id')
                  ->nullable()
                  ->constrained('clientes')
                  ->nullOnDelete();
            
            // Usuario que realizó la venta (vendedor)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->comment('Vendedor que realizó la venta');
            
            // 📄 DATOS DEL COMPROBANTE
            $table->enum('comprobante', ['BOLETA', 'FACTURA', 'TICKET'])->default('BOLETA');
            $table->string('serie', 10)->default('B001')->comment('Serie: B001, F001');
            $table->string('numero', 20)->comment('Número correlativo');
            
            // 💰 MONTOS E IGV
            $table->decimal('igv_porcentaje', 5, 2)->default(18.00)->comment('Porcentaje de IGV');
            $table->decimal('subtotal', 12, 2)->default(0)->comment('Subtotal sin IGV');
            $table->decimal('igv_monto', 12, 2)->default(0)->comment('Monto del IGV');
            $table->decimal('total', 12, 2)->default(0)->comment('Total de la venta');
            
            // 💵 PAGO
            $table->decimal('monto_recibido', 12, 2)->default(0)->comment('Dinero recibido del cliente');
            $table->decimal('vuelto', 12, 2)->default(0)->comment('Vuelto entregado');
            
            // 📅 FECHAS
            $table->dateTime('fecha_emision')->comment('Fecha y hora de la venta');
            $table->date('fecha_vencimiento')->nullable()->comment('Fecha de vencimiento del pago');
            
            // ✅ ESTADO
            // COMPLETADA = Venta finalizada
            // ANULADA = Venta anulada
            // PENDIENTE = Venta pendiente de pago
            $table->enum('estado', ['COMPLETADA', 'ANULADA', 'PENDIENTE'])->default('COMPLETADA');
            
            $table->timestamps();
            
            // 🔍 Índices
            $table->index('fecha_emision');
            $table->index(['serie', 'numero']); // Para buscar comprobantes
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
