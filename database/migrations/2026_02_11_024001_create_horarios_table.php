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
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();
            $table->string('nombre', 100);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->json('dias_laborales');
            $table->integer('tolerancia_minutos')->default(0);
            $table->decimal('sueldo_base', 12, 2)->default(0);
            $table->decimal('descuento_falta', 10, 2)->default(0);
            $table->decimal('descuento_minuto', 10, 2)->default(0);
            $table->decimal('pago_hora_extra', 10, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
