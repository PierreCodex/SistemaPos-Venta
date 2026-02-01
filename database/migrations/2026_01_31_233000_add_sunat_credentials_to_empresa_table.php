<?php
/**
 * Agregado por Antigravity - Configuración de Sunat Dinámica
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresa', function (Blueprint $table) {
            // Credenciales Clave SOL
            $table->string('sunat_sol_user')->nullable()->after('moneda');
            $table->string('sunat_sol_pass')->nullable()->after('sunat_sol_user');
            
            // Certificado (.pem)
            $table->string('sunat_cert_path')->nullable()->after('sunat_sol_pass');
            
            // API Sunat (Opcional para algunos servicios, necesario para otros)
            $table->string('sunat_client_id')->nullable()->after('sunat_cert_path');
            $table->string('sunat_client_secret')->nullable()->after('sunat_client_id');
            
            // Entorno de ejecución
            $table->boolean('sunat_produccion')->default(false)->after('sunat_client_secret');
        });
    }

    public function down(): void
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->dropColumn([
                'sunat_sol_user',
                'sunat_sol_pass',
                'sunat_cert_path',
                'sunat_client_id',
                'sunat_client_secret',
                'sunat_produccion'
            ]);
        });
    }
};
