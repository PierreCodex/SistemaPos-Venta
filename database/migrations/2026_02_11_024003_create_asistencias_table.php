<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 12)->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('horario_id')->constrained('horarios')->onDelete('cascade');
            $table->date('fecha');
            $table->time('hora_entrada')->nullable();
            $table->time('hora_inicio_refrigerio')->nullable();
            $table->time('hora_fin_refrigerio')->nullable();
            $table->time('hora_salida')->nullable();
            $table->integer('minutos_tardanza')->default(0);
            $table->integer('minutos_trabajados')->default(0);
            $table->integer('minutos_extra')->default(0);
            $table->enum('estado', ['PENDIENTE', 'PRESENTE', 'TARDANZA', 'FALTA', 'PERMISO', 'VACACIONES'])->default('PENDIENTE');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
