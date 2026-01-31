<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * 📄 TUTORIAL: Migración de Comprobantes Electrónicos
 * =====================================================
 * 
 * Esta tabla almacena los comprobantes electrónicos generados
 * para SUNAT (Facturas, Boletas, Notas de Crédito/Débito)
 * y también las Notas de Venta (no electrónicas).
 * 
 * 💡 CONCEPTOS:
 * - Cada VENTA puede tener un COMPROBANTE ELECTRÓNICO
 * - Los comprobantes electrónicos se envían a SUNAT
 * - Las Notas de Venta NO se envían a SUNAT
 * - Se almacenan XML, PDF, CDR (Constancia de Recepción)
 * 
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes_electronicos', function (Blueprint $table) {
            $table->id();
            
            // 🔗 RELACIÓN CON VENTA
            $table->foreignId('venta_id')
                  ->constrained('ventas')
                  ->onDelete('cascade')
                  ->comment('Venta asociada al comprobante');
            
            // 📄 DATOS DEL COMPROBANTE
            $table->enum('tipo_comprobante', [
                'FACTURA',           // 01 - Factura electrónica
                'BOLETA',            // 03 - Boleta electrónica
                'NOTA_CREDITO',      // 07 - Nota de crédito
                'NOTA_DEBITO',       // 08 - Nota de débito
                'NOTA_VENTA'         // Nota de venta (NO electrónica)
            ])->comment('Tipo de comprobante');
            
            $table->string('serie', 10)->comment('Serie del comprobante (F001, B001, NV01)');
            $table->string('numero', 20)->comment('Número correlativo');
            $table->dateTime('fecha_emision')->comment('Fecha y hora de emisión');
            
            // 💰 MONTOS
            $table->string('moneda', 3)->default('PEN')->comment('Código de moneda (PEN, USD)');
            $table->string('tipo_operacion', 10)->default('0101')->comment('Código de tipo de operación SUNAT');
            $table->decimal('subtotal', 12, 2)->default(0)->comment('Base imponible (sin IGV)');
            $table->decimal('igv', 12, 2)->default(0)->comment('Monto del IGV');
            $table->decimal('total', 12, 2)->default(0)->comment('Total del comprobante');
            
            // 📁 ARCHIVOS GENERADOS
            $table->string('xml_path', 500)->nullable()->comment('Ruta del archivo XML generado');
            $table->string('pdf_path', 500)->nullable()->comment('Ruta del archivo PDF generado');
            $table->string('cdr_path', 500)->nullable()->comment('Ruta del CDR (Constancia de Recepción SUNAT)');
            
            // 🔐 FIRMA DIGITAL
            $table->string('hash', 100)->nullable()->comment('Hash de la firma digital');
            $table->text('qr_data')->nullable()->comment('Datos para generar código QR');
            
            // 📡 ESTADO SUNAT
            $table->enum('estado_sunat', [
                'PENDIENTE',         // No enviado aún
                'ACEPTADO',          // Aceptado por SUNAT
                'RECHAZADO',         // Rechazado por SUNAT
                'ANULADO',           // Comprobante anulado
                'NO_APLICA'          // Para notas de venta (no se envían)
            ])->default('PENDIENTE')->comment('Estado del comprobante en SUNAT');
            
            $table->string('codigo_sunat', 10)->nullable()->comment('Código de respuesta de SUNAT');
            $table->text('mensaje_sunat')->nullable()->comment('Mensaje de respuesta de SUNAT');
            $table->dateTime('fecha_envio_sunat')->nullable()->comment('Fecha de envío a SUNAT');
            
            // 🎯 CONTROL
            $table->boolean('es_electronico')->default(true)->comment('TRUE para electrónicos, FALSE para notas de venta');
            $table->text('observaciones')->nullable()->comment('Observaciones adicionales');
            
            $table->timestamps();
            
            // 🔍 ÍNDICES
            $table->index('venta_id');
            $table->index(['serie', 'numero']); // Para búsqueda rápida de comprobantes
            $table->index('fecha_emision');
            $table->index('estado_sunat');
            $table->unique(['tipo_comprobante', 'serie', 'numero']); // No duplicar comprobantes
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes_electronicos');
    }
};
