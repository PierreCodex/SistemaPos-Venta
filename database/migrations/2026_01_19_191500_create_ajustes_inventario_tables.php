<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * Tablas: ajustes_inventario y detalle_ajustes_inventario
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ajustes_inventario', function (Blueprint $table) {
            $table->id();

            $table->enum('tipo', ['ENTRADA', 'SALIDA', 'CONTEO']);
            
            $table->enum('motivo', [
                'MERMA', 'ROBO', 'VENCIMIENTO', 'ERROR_CONTEO', 'DONACION', 'OTRO'
            ]);
            
            $table->text('descripcion')->nullable();
            
            $table->foreignId('user_id')
                  ->constrained('users');
            
            $table->dateTime('fecha');
            
            $table->enum('estado', ['BORRADOR', 'APLICADO', 'ANULADO'])
                  ->default('BORRADOR');
            
            $table->timestamps();
        });

        Schema::create('detalle_ajustes_inventario', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ajuste_id')
                  ->constrained('ajustes_inventario')
                  ->cascadeOnDelete();
            
            $table->foreignId('producto_id')
                  ->constrained('productos');
            
            $table->decimal('stock_sistema', 12, 3)
                  ->comment('Stock según sistema');
            $table->decimal('stock_fisico', 12, 3)
                  ->comment('Stock contado físicamente');
            $table->decimal('diferencia', 12, 3)
                  ->comment('físico - sistema');
            
            $table->string('observacion', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_ajustes_inventario');
        Schema::dropIfExists('ajustes_inventario');
    }
};
