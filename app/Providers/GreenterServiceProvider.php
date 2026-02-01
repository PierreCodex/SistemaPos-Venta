<?php

namespace App\Providers;

use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class GreenterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(See::class, function ($app) {
            $see = new See();
            
            // Obtener datos de la empresa desde la DB
            $empresa = \App\Models\Empresa::principal();
            
            // 1. Configurar certificado
            $certificatePath = ($empresa && $empresa->sunat_cert_path) 
                ? storage_path('app/public/certificados/' . $empresa->sunat_cert_path)
                : config('greenter.certificate.path');
            
            if (!File::exists($certificatePath)) {
                // Si es modo producción y no hay certificado, lanzar error
                if ($empresa && $empresa->sunat_produccion) {
                    throw new \Exception("Certificado digital no encontrado para producción.");
                }
                // En modo beta/pruebas, intentamos cargar el certificado de prueba del storage o config
            } else {
                $see->setCertificate(File::get($certificatePath));
            }
            
            // 2. Configurar endpoint según el modo (Prioriza DB sobre config)
            $isProduction = $empresa ? (bool)$empresa->sunat_produccion : (config('greenter.mode') === 'production');
            
            if ($isProduction) {
                $see->setService(SunatEndpoints::FE_PRODUCCION);
            } else {
                $see->setService(SunatEndpoints::FE_BETA);
            }
            
            // 3. Configurar credenciales Clave SOL (Prioriza DB sobre config)
            $ruc = $empresa ? $empresa->ruc : config('greenter.credentials.ruc');
            $user = ($empresa && $empresa->sunat_sol_user) ? $empresa->sunat_sol_user : config('greenter.credentials.username');
            $pass = ($empresa && $empresa->sunat_sol_pass) ? $empresa->sunat_sol_pass : config('greenter.credentials.password');
            
            $see->setClaveSOL($ruc, $user, $pass);
            
            return $see;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Crear directorios necesarios si no existen
        $directories = [
            storage_path('certificates'),
            config('greenter.storage.xml'),
            config('greenter.storage.pdf'),
            config('greenter.storage.cdr'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
        }
    }
}
