<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * Actualización de Ventas para POS completo
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Relación con caja
            $table->foreignId('caja_sesion_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('caja_sesiones')
                  ->comment('Turno donde se vendió');
            
            // Método de pago mejorado
            $table->enum('metodo_pago', [
                'EFECTIVO', 'TARJETA', 'YAPE', 'PLIN', 'TRANSFERENCIA', 'MIXTO', 'CREDITO'
            ])->default('EFECTIVO')->after('numero');
            
            // Descuento
            $table->decimal('descuento', 12, 2)->default(0)->after('subtotal');
            
            // Pagos mixtos detallados
            $table->decimal('pago_efectivo', 12, 2)->default(0)->after('vuelto');
            $table->decimal('pago_tarjeta', 12, 2)->default(0)->after('pago_efectivo');
            $table->decimal('pago_yape', 12, 2)->default(0)->after('pago_tarjeta');
            $table->decimal('pago_plin', 12, 2)->default(0)->after('pago_yape');
            $table->decimal('pago_transferencia', 12, 2)->default(0)->after('pago_plin');
            
            // Ventas a crédito
            $table->boolean('es_credito')->default(false)->after('pago_transferencia');
            $table->date('fecha_vencimiento_credito')->nullable()->after('es_credito');
            $table->enum('estado_pago', ['PAGADO', 'PENDIENTE', 'PARCIAL'])
                  ->default('PAGADO')->after('fecha_vencimiento_credito');
            $table->decimal('saldo_pendiente', 12, 2)->default(0)->after('estado_pago');
            
            // Observaciones y anulación
            $table->text('observaciones')->nullable()->after('estado');
            $table->text('motivo_anulacion')->nullable()->after('observaciones');
            $table->dateTime('fecha_anulacion')->nullable()->after('motivo_anulacion');
            $table->foreignId('user_anulacion_id')
                  ->nullable()
                  ->after('fecha_anulacion')
                  ->constrained('users');

            // Índices
            $table->index('estado', 'idx_ventas_estado');
            $table->index('cliente_id', 'idx_ventas_cliente');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['caja_sesion_id']);
            $table->dropForeign(['user_anulacion_id']);
            $table->dropIndex('idx_ventas_estado');
            $table->dropIndex('idx_ventas_cliente');
            
            $table->dropColumn([
                'caja_sesion_id',
                'metodo_pago',
                'descuento',
                'pago_efectivo',
                'pago_tarjeta',
                'pago_yape',
                'pago_plin',
                'pago_transferencia',
                'es_credito',
                'fecha_vencimiento_credito',
                'estado_pago',
                'saldo_pendiente',
                'observaciones',
                'motivo_anulacion',
                'fecha_anulacion',
                'user_anulacion_id'
            ]);
        });
    }
};
