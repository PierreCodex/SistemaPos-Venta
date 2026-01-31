<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * =====================================================
 * Tablas: empresa, configuracion y actividad_log
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        // Datos de la empresa
        Schema::create('empresa', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 11);
            $table->string('razon_social', 200);
            $table->string('nombre_comercial', 200)->nullable();
            $table->text('direccion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('logo', 255)->nullable();
            $table->decimal('igv_porcentaje', 5, 2)->default(18.00);
            $table->string('moneda', 3)->default('PEN');
            $table->timestamps();
        });

        // Configuración del sistema
        Schema::create('configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 100)->unique();
            $table->text('valor')->nullable();
            $table->enum('tipo', ['texto', 'numero', 'boolean', 'json'])->default('texto');
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();
        });

        // Log de actividades (auditoría)
        Schema::create('actividad_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('accion', 100);
            $table->string('modelo', 100)->nullable();
            $table->unsignedBigInteger('modelo_id')->nullable();
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id', 'idx_log_user');
            $table->index(['modelo', 'modelo_id'], 'idx_log_modelo');
            $table->index('created_at', 'idx_log_fecha');
        });

        // Historial de precios de productos
        Schema::create('productos_historial_precios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->cascadeOnDelete();
            $table->decimal('precio_compra_anterior', 10, 2);
            $table->decimal('precio_compra_nuevo', 10, 2);
            $table->decimal('precio_venta_anterior', 10, 2);
            $table->decimal('precio_venta_nuevo', 10, 2);
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('motivo', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('producto_id', 'idx_hist_precio_producto');
        });

        // Datos semilla para empresa
        DB::table('empresa')->insert([
            'ruc' => '00000000000',
            'razon_social' => 'MI EMPRESA S.A.C.',
            'nombre_comercial' => 'MI TIENDA',
            'igv_porcentaje' => 18.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Datos semilla para configuración
        DB::table('configuracion')->insert([
            ['clave' => 'permitir_venta_sin_stock', 'valor' => 'false', 'tipo' => 'boolean', 
             'descripcion' => 'Permite vender productos sin stock', 'created_at' => now()],
            ['clave' => 'alertar_stock_minimo', 'valor' => 'true', 'tipo' => 'boolean', 
             'descripcion' => 'Mostrar alerta cuando el stock esté bajo', 'created_at' => now()],
            ['clave' => 'imprimir_automatico', 'valor' => 'true', 'tipo' => 'boolean', 
             'descripcion' => 'Imprimir comprobante automáticamente al vender', 'created_at' => now()],
            ['clave' => 'decimales_cantidad', 'valor' => '3', 'tipo' => 'numero', 
             'descripcion' => 'Cantidad de decimales para cantidades', 'created_at' => now()],
            ['clave' => 'decimales_precio', 'valor' => '2', 'tipo' => 'numero', 
             'descripcion' => 'Cantidad de decimales para precios', 'created_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('productos_historial_precios');
        Schema::dropIfExists('actividad_log');
        Schema::dropIfExists('configuracion');
        Schema::dropIfExists('empresa');
    }
};
