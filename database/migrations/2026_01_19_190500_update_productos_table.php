<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * Actualización de Productos para POS Abarrotes
 * =====================================================
 * 
 * Cambios principales:
 * - Stock con decimales (para KG, LTR)
 * - Código de barras
 * - Precio mayorista
 * - Campos de control IGV y servicios
 * 
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Código de barras
            $table->string('codigo_barras', 50)->nullable()->after('codigo')
                  ->comment('EAN-13, UPC, etc.');
            
            // Cambiar stock a decimal para productos a granel
            // Nota: Esto requiere que no haya FKs activas
        });

        // Modificar stock a decimal (MySQL específico)
        DB::statement('ALTER TABLE productos MODIFY stock DECIMAL(12,3) NOT NULL DEFAULT 0.000');
        DB::statement('ALTER TABLE productos MODIFY stock_minimo DECIMAL(12,3) NOT NULL DEFAULT 5.000');

        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('stock_maximo', 12, 3)->nullable()->after('stock_minimo')
                  ->comment('Para alertas de sobrestock');
            
            // Precios mayorista
            $table->decimal('precio_mayorista', 10, 2)->nullable()->after('precio_venta')
                  ->comment('Precio por volumen');
            $table->decimal('cantidad_mayorista', 12, 3)->nullable()->after('precio_mayorista')
                  ->comment('Cantidad mínima para precio mayorista');
            
            // Control de impuestos y servicios
            $table->boolean('aplica_igv')->default(true)->after('cantidad_mayorista')
                  ->comment('Si el producto está gravado');
            $table->boolean('es_servicio')->default(false)->after('aplica_igv')
                  ->comment('No afecta stock si es true');
            $table->boolean('permite_venta_negativa')->default(false)->after('es_servicio')
                  ->comment('Vender sin stock');
            
            $table->softDeletes()->after('updated_at');

            // Índices adicionales
            $table->index('codigo_barras', 'idx_productos_barcode');
            $table->index(['stock', 'stock_minimo'], 'idx_productos_stock');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex('idx_productos_barcode');
            $table->dropIndex('idx_productos_stock');
            $table->dropColumn([
                'codigo_barras',
                'stock_maximo',
                'precio_mayorista',
                'cantidad_mayorista',
                'aplica_igv',
                'es_servicio',
                'permite_venta_negativa'
            ]);
            $table->dropSoftDeletes();
        });

        // Revertir stock a integer
        DB::statement('ALTER TABLE productos MODIFY stock INT NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE productos MODIFY stock_minimo INT NOT NULL DEFAULT 5');
    }
};
