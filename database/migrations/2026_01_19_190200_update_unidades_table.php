<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * =====================================================
 * Actualización de Unidades + Datos Semilla
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unidades', function (Blueprint $table) {
            $table->boolean('permite_decimales')->default(false)->after('nombre')
                  ->comment('KG, LTR = true | UND, PAQ = false');
        });

        // Actualizar código a único si no lo está
        if (!$this->hasUniqueIndex('unidades', 'codigo')) {
            Schema::table('unidades', function (Blueprint $table) {
                $table->unique('codigo', 'unidades_codigo_unique');
            });
        }

        // Insertar unidades por defecto si la tabla está vacía
        if (DB::table('unidades')->count() === 0) {
            DB::table('unidades')->insert([
                ['codigo' => 'KG', 'nombre' => 'Kilogramos', 'permite_decimales' => true, 'estado' => true, 'created_at' => now()],
                ['codigo' => 'UND', 'nombre' => 'Unidades', 'permite_decimales' => false, 'estado' => true, 'created_at' => now()],
                ['codigo' => 'LTR', 'nombre' => 'Litros', 'permite_decimales' => true, 'estado' => true, 'created_at' => now()],
                ['codigo' => 'GR', 'nombre' => 'Gramos', 'permite_decimales' => true, 'estado' => true, 'created_at' => now()],
                ['codigo' => 'ML', 'nombre' => 'Mililitros', 'permite_decimales' => true, 'estado' => true, 'created_at' => now()],
                ['codigo' => 'PAQ', 'nombre' => 'Paquete', 'permite_decimales' => false, 'estado' => true, 'created_at' => now()],
                ['codigo' => 'CAJ', 'nombre' => 'Caja', 'permite_decimales' => false, 'estado' => true, 'created_at' => now()],
                ['codigo' => 'DOC', 'nombre' => 'Docena', 'permite_decimales' => false, 'estado' => true, 'created_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('unidades', function (Blueprint $table) {
            $table->dropColumn('permite_decimales');
        });
    }

    private function hasUniqueIndex(string $table, string $column): bool
    {
        $indexes = Schema::getIndexes($table);
        foreach ($indexes as $index) {
            if (in_array($column, $index['columns']) && $index['unique']) {
                return true;
            }
        }
        return false;
    }
};
