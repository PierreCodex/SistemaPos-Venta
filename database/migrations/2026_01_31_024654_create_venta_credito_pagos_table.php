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
        Schema::create('venta_credito_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->decimal('monto', 12, 2);
            $table->string('metodo_pago'); // EFECTIVO, YAPE, PLIN, etc.
            $table->dateTime('fecha_pago');
            $table->string('numero_operacion')->nullable();
            $table->foreignId('user_id')->constrained('users'); // Usuario que recibe el pago
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_credito_pagos');
    }
};
