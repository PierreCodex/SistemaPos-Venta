<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            // Agregar campo para relacionar con categorías globales
            $table->foreignId('categoria_global_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('categorias_globales')
                  ->nullOnDelete()
                  ->comment('FK a categorias_globales');
            
            // Eliminar el campo categoria_id anterior (auto-referencia)
            $table->dropForeign(['categoria_id']);
            $table->dropColumn('categoria_id');
        });
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropForeign(['categoria_global_id']);
            $table->dropColumn('categoria_global_id');
            
            // Restaurar campo anterior
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->foreign('categoria_id')
                  ->references('id')
                  ->on('categorias')
                  ->onDelete('set null');
        });
    }
};
