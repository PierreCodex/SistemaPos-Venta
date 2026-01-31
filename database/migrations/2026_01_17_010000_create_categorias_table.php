<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * 🎓 TUTORIAL: Migración de Categorías
 * =====================================================
 * 
 * Las migraciones son como "scripts" que crean tablas.
 * En PHP puro harías: CREATE TABLE categorias (...)
 * En Laravel usamos Schema::create()
 * 
 * =====================================================
 */
return new class extends Migration
{
    /**
     * up() = Se ejecuta cuando corres: php artisan migrate
     * Aquí CREAS la tabla
     */
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            // 🔑 id = Clave primaria autoincremental (equivale a: INT AUTO_INCREMENT PRIMARY KEY)
            $table->id();
            
            // 📁 categoria_id = Para subcategorías (categoría padre)
            // unsignedBigInteger = Número positivo grande (para relacionar con id)
            // nullable() = Puede ser NULL (las categorías principales no tienen padre)
            $table->unsignedBigInteger('categoria_id')->nullable()->comment('ID de categoría padre para subcategorías');
            
            // 📝 Campos de texto
            $table->string('nombre', 100)->comment('Nombre de la categoría');
            $table->text('descripcion')->nullable()->comment('Descripción opcional');
            
            // ✅ Estado: 1 = Activo, 0 = Inactivo
            $table->boolean('estado')->default(true)->comment('1=Activo, 0=Inactivo');
            
            // 📅 created_at y updated_at (Laravel los maneja automáticamente)
            $table->timestamps();
            
            // 🔗 Llave foránea: categoria_id hace referencia a id de la misma tabla (auto-relación)
            $table->foreign('categoria_id')
                  ->references('id')
                  ->on('categorias')
                  ->onDelete('set null'); // Si se elimina la categoría padre, el hijo queda sin padre
        });
    }

    /**
     * down() = Se ejecuta cuando corres: php artisan migrate:rollback
     * Aquí ELIMINAS la tabla (para deshacer cambios)
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
