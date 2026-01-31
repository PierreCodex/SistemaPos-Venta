<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================
 * Tablas de Spatie Permission (Roles y Permisos)
 * =====================================================
 * 
 * Estas tablas son requeridas por el paquete spatie/laravel-permission
 * Comando para instalar: composer require spatie/laravel-permission
 * 
 * =====================================================
 */
return new class extends Migration
{
    public function up(): void
    {
        // Tabla de permisos
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('guard_name', 191)->default('web');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        // Tabla de roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('guard_name', 191)->default('web');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        // Asignación de roles a modelos (usuarios)
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->string('model_type', 191);
            $table->unsignedBigInteger('model_id');

            $table->primary(['role_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
        });

        // Asignación de permisos a modelos (usuarios)
        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->string('model_type', 191);
            $table->unsignedBigInteger('model_id');

            $table->primary(['permission_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
        });

        // Asignación de permisos a roles
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();

            $table->primary(['permission_id', 'role_id']);
        });

        // Insertar roles básicos
        DB::table('roles')->insert([
            ['name' => 'Admin', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cajero', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Almacenero', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Supervisor', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
};
