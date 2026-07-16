<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * Tabla: producto_presentaciones
 * =====================================================
 *
 * Permite vender un mismo producto en varias unidades
 * (unidad suelta, paquete, caja x24) manteniendo un solo
 * inventario expresado en la UNIDAD BASE del producto.
 *
 * El factor pertenece a la relación producto-unidad, no a
 * la unidad: "Caja" son 24 gaseosas pero 12 aceites.
 *
 * A partir de aquí, productos.stock se interpreta SIEMPRE
 * en unidad base. El backfill no transforma ningún stock:
 * hasta hoy el factor era implícitamente 1, así que el
 * stock existente YA está en unidad base.
 *
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_presentaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->restrictOnDelete()
                  ->comment('FK a productos');

            $table->foreignId('unidad_id')
                  ->constrained('unidades')
                  ->restrictOnDelete()
                  ->comment('Unidad en la que se expresa esta presentación');

            // Cuántas unidades base contiene esta presentación.
            // Base = 1. Caja x24 = 24. Bolsa de 500g con base gramo = 500.
            $table->decimal('factor', 12, 4)->default(1.0000)
                  ->comment('Unidades base que contiene esta presentación');

            // El precio vive aquí: una caja NO cuesta 24x el precio unitario.
            $table->decimal('precio_venta', 10, 2)->default(0.00);

            // Código de barras propio de la presentación (la caja trae el suyo).
            $table->string('codigo_barras', 50)->nullable()->unique();

            $table->boolean('es_base')->default(false)
                  ->comment('La presentación con factor 1 en la que se lleva el stock');
            $table->boolean('estado')->default(true);

            $table->timestamps();

            // Un producto no puede tener dos presentaciones en la misma unidad
            $table->unique(['producto_id', 'unidad_id'], 'uq_presentacion_producto_unidad');
            $table->index('producto_id', 'idx_presentacion_producto');
        });

        $this->backfill();
    }

    /**
     * Crea la presentación base de cada producto existente.
     *
     * Idempotente y sin pérdida: replica la unidad y el precio que el
     * producto ya tiene, con factor 1. El comportamiento del sistema
     * queda idéntico al de antes de esta migración.
     */
    private function backfill(): void
    {
        // Productos sin unidad asignada: se les asigna la unidad genérica UND.
        // Un producto sin unidad es implícitamente "unidades", y la presentación
        // base necesita una unidad para poder convertir.
        $unidadPorDefecto = DB::table('unidades')->where('codigo', 'UND')->value('id');

        if (!$unidadPorDefecto) {
            $unidadPorDefecto = DB::table('unidades')->insertGetId([
                'codigo' => 'UND',
                'nombre' => 'Unidades',
                'permite_decimales' => false,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Incluye productos soft-deleted: su histórico de ventas sigue vivo
        // y en la Fase 2 las anulaciones necesitarán resolver su presentación.
        DB::table('productos')->orderBy('id')->chunkById(500, function ($productos) use ($unidadPorDefecto) {
            $filas = [];

            foreach ($productos as $producto) {
                $filas[] = [
                    'producto_id'   => $producto->id,
                    'unidad_id'     => $producto->unidad_id ?? $unidadPorDefecto,
                    'factor'        => 1.0000,
                    'precio_venta'  => $producto->precio_venta,
                    // El código de barras NO se copia: productos.codigo_barras no es
                    // único y copiarlo reventaría el índice único de esta tabla.
                    // El escaneo del producto sigue resolviendo a su presentación base.
                    'codigo_barras' => null,
                    'es_base'       => true,
                    'estado'        => true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }

            if ($filas) {
                DB::table('producto_presentaciones')->insert($filas);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_presentaciones');
    }
};
