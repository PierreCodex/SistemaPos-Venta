<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * Tabla: caja_sesiones (Turnos de Caja)
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja_sesiones', function (Blueprint $table) {
            $table->id();
            
            // Usuario que abre la caja
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->comment('Usuario que abre caja');
            
            // Usuario que cierra (puede ser diferente)
            $table->foreignId('user_cierre_id')
                  ->nullable()
                  ->constrained('users')
                  ->comment('Usuario que cierra caja');
            
            // Montos
            $table->decimal('monto_inicial', 12, 2)->default(0)
                  ->comment('Efectivo inicial al abrir');
            $table->decimal('monto_final', 12, 2)->nullable()
                  ->comment('Efectivo físico al cierre');
            $table->decimal('monto_esperado', 12, 2)->nullable()
                  ->comment('Calculado: inicial + ingresos - egresos');
            $table->decimal('diferencia', 12, 2)->nullable()
                  ->comment('final - esperado (+ sobrante, - faltante)');
            
            // Resúmenes rápidos
            $table->decimal('total_ventas', 12, 2)->default(0);
            $table->decimal('total_ingresos', 12, 2)->default(0);
            $table->decimal('total_egresos', 12, 2)->default(0);
            
            // Fechas
            $table->dateTime('fecha_apertura')->useCurrent();
            $table->dateTime('fecha_cierre')->nullable();
            
            // Control
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['ABIERTA', 'CERRADA', 'ARQUEADA'])->default('ABIERTA');
            
            $table->timestamps();

            // Índices
            $table->index('fecha_apertura', 'idx_caja_fecha');
            $table->index('estado', 'idx_caja_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_sesiones');
    }
};
