<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * =====================================================
 * Actualización de Clientes para POS Abarrotes
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        // Modificar el ENUM para incluir 'VARIOS'
        DB::statement("ALTER TABLE clientes MODIFY COLUMN tipo_documento ENUM('DNI', 'RUC', 'CE', 'PASAPORTE', 'VARIOS') DEFAULT 'DNI'");

        // Verificar y agregar columnas que no existan
        if (!Schema::hasColumn('clientes', 'fecha_nacimiento')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->date('fecha_nacimiento')->nullable()->after('direccion');
            });
        }

        if (!Schema::hasColumn('clientes', 'puntos_acumulados')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->integer('puntos_acumulados')->default(0)->after('fecha_nacimiento');
            });
        }

        if (!Schema::hasColumn('clientes', 'limite_credito')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->decimal('limite_credito', 12, 2)->default(0)->after('puntos_acumulados');
            });
        }

        if (!Schema::hasColumn('clientes', 'saldo_pendiente')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->decimal('saldo_pendiente', 12, 2)->default(0)->after('limite_credito');
            });
        }

        if (!Schema::hasColumn('clientes', 'notas')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->text('notas')->nullable()->after('saldo_pendiente');
            });
        }

        if (!Schema::hasColumn('clientes', 'deleted_at')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        // Agregar índices (ignorar si ya existen)
        try {
            Schema::table('clientes', function (Blueprint $table) {
                $table->index('numero_documento', 'idx_clientes_documento');
            });
        } catch (\Exception $e) {
            // Índice ya existe
        }

        try {
            Schema::table('clientes', function (Blueprint $table) {
                $table->index('nombre', 'idx_clientes_nombre');
            });
        } catch (\Exception $e) {
            // Índice ya existe
        }

        // Insertar cliente genérico si no existe
        if (DB::table('clientes')->where('numero_documento', '00000000')->count() === 0) {
            DB::table('clientes')->insert([
                'tipo_documento' => 'VARIOS',
                'numero_documento' => '00000000',
                'nombre' => 'CLIENTE GENÉRICO',
                'direccion' => 'S/D',
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Eliminar índices
        try {
            Schema::table('clientes', function (Blueprint $table) {
                $table->dropIndex('idx_clientes_documento');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('clientes', function (Blueprint $table) {
                $table->dropIndex('idx_clientes_nombre');
            });
        } catch (\Exception $e) {}

        // Eliminar columnas
        $columnsToRemove = ['fecha_nacimiento', 'puntos_acumulados', 'limite_credito', 'saldo_pendiente', 'notas', 'deleted_at'];
        foreach ($columnsToRemove as $column) {
            if (Schema::hasColumn('clientes', $column)) {
                Schema::table('clientes', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
