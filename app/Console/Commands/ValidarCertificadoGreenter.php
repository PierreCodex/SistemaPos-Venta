<?php

namespace App\Console\Commands;

use App\Services\GreenterService;
use Illuminate\Console\Command;

class ValidarCertificadoGreenter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'greenter:validate-certificate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Valida el certificado digital de SUNAT para facturación electrónica';

    /**
     * Execute the console command.
     */
    public function handle(GreenterService $greenterService): int
    {
        $this->info('🔐 Validando certificado digital de SUNAT...');
        $this->newLine();

        $resultado = $greenterService->validarCertificado();

        if ($resultado['valid']) {
            $this->components->success('✅ Certificado válido');
            $this->info("📅 Expira el: {$resultado['expires_at']}");
            $this->newLine();
            
            // Mostrar configuración actual
            $this->info('⚙️  Configuración actual:');
            $this->table(
                ['Parámetro', 'Valor'],
                [
                    ['Modo', config('greenter.mode')],
                    ['RUC Empresa', config('greenter.company.ruc')],
                    ['Razón Social', config('greenter.company.razon_social')],
                    ['RUC Clave SOL', config('greenter.credentials.ruc')],
                    ['Usuario SOL', config('greenter.credentials.username')],
                ]
            );

            return Command::SUCCESS;
        } else {
            $this->components->error('❌ Certificado inválido');
            $this->error($resultado['message']);
            return Command::FAILURE;
        }
    }
}
