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
        Schema::table('horarios', function (Blueprint $table) {
            $table->time('hora_inicio_refrigerio')->nullable()->after('hora_fin');
            $table->time('hora_fin_refrigerio')->nullable()->after('hora_inicio_refrigerio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropColumn(['hora_inicio_refrigerio', 'hora_fin_refrigerio']);
        });
    }
};
