<?php

namespace App\Services;

use Greenter\See;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Note;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Servicio base para interactuar con Greenter y SUNAT
 * 
 * Maneja la generación de XML, envío a SUNAT, consultas de estado,
 * generación de hash y códigos QR.
 */
class GreenterService
{
    protected See $see;

    public function __construct(See $see)
    {
        $this->see = $see;
    }

    /**
     * Envía un comprobante a SUNAT
     * 
     * @param Invoice|Note $document Documento a enviar
     * @return array ['success' => bool, 'xml' => string, 'hash' => string, 'cdr' => string|null, 'code' => string|null, 'message' => string|null]
     */
    public function enviarComprobante($document): array
    {
        try {
            // Generar XML
            $xml = $this->see->getXmlSigned($document);
            $hash = $this->generarHash($xml);

            // Enviar a SUNAT
            $result = $this->see->send($document);

            if (!$result->isSuccess()) {
                // Error en el envío
                $error = $result->getError();
                
                Log::error('Error al enviar comprobante a SUNAT', [
                    'code' => $error->getCode(),
                    'message' => $error->getMessage()
                ]);

                return [
                    'success' => false,
                    'xml' => $xml,
                    'hash' => $hash,
                    'cdr' => null,
                    'code' => $error->getCode(),
                    'message' => $error->getMessage()
                ];
            }

            // Éxito - obtener CDR (Constancia de Recepción)
            $cdr = $result->getCdrResponse();
            $cdrZip = $result->getCdrZip();

            Log::info('Comprobante enviado exitosamente a SUNAT', [
                'code' => $cdr->getCode(),
                'description' => $cdr->getDescription()
            ]);

            return [
                'success' => true,
                'xml' => $xml,
                'hash' => $hash,
                'cdr' => $cdrZip,
                'code' => $cdr->getCode(),
                'message' => $cdr->getDescription(),
                'notes' => $cdr->getNotes() // Observaciones de SUNAT
            ];

        } catch (\Exception $e) {
            Log::error('Excepción al enviar comprobante a SUNAT', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'xml' => $xml ?? null,
                'hash' => $hash ?? null,
                'cdr' => null,
                'code' => 'EXCEPTION',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Consulta el estado de un comprobante en SUNAT
     * 
     * @param string $ruc RUC del emisor
     * @param string $tipoDoc Tipo de documento (01, 03, 07, 08)
     * @param string $serie Serie del comprobante
     * @param string $numero Número del comprobante
     * @return array ['success' => bool, 'code' => string|null, 'message' => string|null]
     */
    public function consultarEstado(string $ruc, string $tipoDoc, string $serie, string $numero): array
    {
        try {
            $result = $this->see->getStatus($tipoDoc, $serie, $numero);

            if (!$result->isSuccess()) {
                $error = $result->getError();
                
                return [
                    'success' => false,
                    'code' => $error->getCode(),
                    'message' => $error->getMessage()
                ];
            }

            $cdr = $result->getCdrResponse();

            return [
                'success' => true,
                'code' => $cdr->getCode(),
                'message' => $cdr->getDescription(),
                'notes' => $cdr->getNotes()
            ];

        } catch (\Exception $e) {
            Log::error('Error al consultar estado en SUNAT', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'code' => 'EXCEPTION',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Genera el hash SHA256 del XML
     */
    public function generarHash(string $xml): string
    {
        return hash('sha256', $xml);
    }

    /**
     * Genera los datos para el código QR
     * Formato: RUC|TIPO_DOC|SERIE|NUMERO|IGV|TOTAL|FECHA|TIPO_DOC_CLIENTE|NUM_DOC_CLIENTE|
     */
    public function generarQR(
        string $ruc,
        string $tipoDoc,
        string $serie,
        string $numero,
        float $igv,
        float $total,
        string $fecha,
        string $tipoDocCliente = '1',
        string $numDocCliente = '00000000'
    ): string {
        return implode('|', [
            $ruc,
            $tipoDoc,
            $serie,
            $numero,
            number_format($igv, 2, '.', ''),
            number_format($total, 2, '.', ''),
            $fecha,
            $tipoDocCliente,
            $numDocCliente,
            ''
        ]);
    }

    /**
     * Guarda el XML en storage
     * 
     * @param string $xml Contenido del XML
     * @param string $filename Nombre del archivo (sin extensión)
     * @return string Ruta relativa del archivo guardado
     */
    public function guardarXml(string $xml, string $filename): string
    {
        $path = 'greenter/xml/' . $filename . '.xml';
        Storage::put($path, $xml);
        return $path;
    }

    /**
     * Guarda el CDR (ZIP) en storage
     * 
     * @param string $cdrZip Contenido del CDR en formato ZIP
     * @param string $filename Nombre del archivo (sin extensión)
     * @return string Ruta relativa del archivo guardado
     */
    public function guardarCdr(string $cdrZip, string $filename): string
    {
        $path = 'greenter/cdr/R-' . $filename . '.zip';
        Storage::put($path, $cdrZip);
        return $path;
    }

    /**
     * Valida que el certificado digital sea válido
     * 
     * @return array ['valid' => bool, 'expires_at' => string|null, 'message' => string]
     */
    public function validarCertificado(): array
    {
        try {
            $certificatePath = config('greenter.certificate.path');

            if (!file_exists($certificatePath)) {
                return [
                    'valid' => false,
                    'expires_at' => null,
                    'message' => 'Certificado no encontrado en: ' . $certificatePath
                ];
            }

            $certificateContent = file_get_contents($certificatePath);
            $cert = openssl_x509_read($certificateContent);

            if (!$cert) {
                return [
                    'valid' => false,
                    'expires_at' => null,
                    'message' => 'No se pudo leer el certificado'
                ];
            }

            $certInfo = openssl_x509_parse($cert);
            $expiresAt = date('Y-m-d H:i:s', $certInfo['validTo_time_t']);
            $now = time();

            if ($certInfo['validTo_time_t'] < $now) {
                return [
                    'valid' => false,
                    'expires_at' => $expiresAt,
                    'message' => 'El certificado expiró el ' . $expiresAt
                ];
            }

            return [
                'valid' => true,
                'expires_at' => $expiresAt,
                'message' => 'Certificado válido hasta ' . $expiresAt
            ];

        } catch (\Exception $e) {
            return [
                'valid' => false,
                'expires_at' => null,
                'message' => 'Error al validar certificado: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene la configuración de la empresa desde config
     */
    public function getCompanyConfig(): array
    {
        return config('greenter.company');
    }

    /**
     * Obtiene las credenciales Clave SOL desde config
     */
    public function getCredentials(): array
    {
        return config('greenter.credentials');
    }

    /**
     * Verifica si está en modo producción
     */
    public function isProduction(): bool
    {
        return config('greenter.mode') === 'production';
    }
}
