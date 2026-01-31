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
            
            // Configurar certificado
            $certificatePath = config('greenter.certificate.path');
            
            if (!File::exists($certificatePath)) {
                throw new \Exception("Certificado no encontrado en: {$certificatePath}");
            }
            
            $see->setCertificate(File::get($certificatePath));
            
            // Configurar endpoint según el modo
            $mode = config('greenter.mode', 'beta');
            
            if ($mode === 'production') {
                $see->setService(SunatEndpoints::FE_PRODUCCION);
            } else {
                $see->setService(SunatEndpoints::FE_BETA);
            }
            
            // Configurar credenciales Clave SOL
            $see->setClaveSOL(
                config('greenter.credentials.ruc'),
                config('greenter.credentials.username'),
                config('greenter.credentials.password')
            );
            
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
