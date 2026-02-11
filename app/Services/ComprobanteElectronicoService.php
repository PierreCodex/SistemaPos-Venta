<?php

namespace App\Services;

use App\Models\Venta;
use App\Models\ComprobanteElectronico;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Company;
use Greenter\Model\Company\Address;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Servicio principal para gestionar comprobantes electrónicos
 * 
 * Orquesta la generación de facturas, boletas y notas de venta,
 * integrando con Greenter para envío a SUNAT.
 */
class ComprobanteElectronicoService
{
    protected GreenterService $greenterService;
    protected PdfService $pdfService;

    public function __construct(
        GreenterService $greenterService,
        PdfService $pdfService
    ) {
        $this->greenterService = $greenterService;
        $this->pdfService = $pdfService;
    }

    /**
     * Genera un comprobante electrónico desde una venta
     * 
     * @param Venta $venta Venta para la cual generar el comprobante
     * @param string $tipoComprobante FACTURA, BOLETA o NOTA_VENTA
     * @return ComprobanteElectronico
     */
    public function generarDesdeVenta(Venta $venta, string $tipoComprobante): ComprobanteElectronico
    {
        return DB::transaction(function () use ($venta, $tipoComprobante) {
            // Verificar si ya existe un comprobante para esta venta
            if ($venta->comprobanteElectronico) {
                throw new \Exception('Esta venta ya tiene un comprobante electrónico generado');
            }

            // Determinar si es electrónico
            $esElectronico = in_array($tipoComprobante, ['FACTURA', 'BOLETA']);

            // Generar serie y número
            $serie = $this->obtenerSerie($tipoComprobante);
            $numero = $this->generarNumeroCorrelativo($tipoComprobante, $serie);

            // Crear registro del comprobante
            $comprobante = ComprobanteElectronico::create([
                'venta_id' => $venta->id,
                'tipo_comprobante' => $tipoComprobante,
                'serie' => $serie,
                'numero' => $numero,
                'fecha_emision' => $venta->fecha_emision,
                'moneda' => 'PEN',
                'tipo_operacion' => '0101', // Venta interna
                'subtotal' => $venta->subtotal,
                'igv' => $venta->igv_monto,
                'total' => $venta->total,
                'es_electronico' => $esElectronico,
                'estado_sunat' => $esElectronico ? 'PENDIENTE' : 'NO_APLICA'
            ]);

            // Si es electrónico, generar y enviar a SUNAT
            if ($esElectronico) {
                $this->generarYEnviarASunat($comprobante, $venta);
            }

            // PDFs are now generated on-demand by the controller using PdfService

            return $comprobante->fresh();
        });
    }

    /**
     * Genera el documento Greenter y lo envía a SUNAT
     */
    protected function generarYEnviarASunat(ComprobanteElectronico $comprobante, Venta $venta): void
    {
        try {
            // Construir documento según tipo
            if ($comprobante->tipo_comprobante === 'FACTURA') {
                $document = $this->construirFactura($comprobante, $venta);
            } elseif ($comprobante->tipo_comprobante === 'BOLETA') {
                $document = $this->construirBoleta($comprobante, $venta);
            } else {
                throw new \Exception('Tipo de comprobante no soportado para envío a SUNAT');
            }

            // Enviar a SUNAT
            $resultado = $this->greenterService->enviarComprobante($document);

            // Guardar archivos
            $xmlPath = $this->greenterService->guardarXml(
                $resultado['xml'],
                $comprobante->serie . '-' . $comprobante->numero
            );

            $cdrPath = null;
            if ($resultado['cdr']) {
                $cdrPath = $this->greenterService->guardarCdr(
                    $resultado['cdr'],
                    $comprobante->serie . '-' . $comprobante->numero
                );
            }

            // Generar datos QR
            $qrData = $this->greenterService->generarQR(
                config('greenter.company.ruc'),
                $comprobante->getCodigoTipoSunat(),
                $comprobante->serie,
                $comprobante->numero,
                $comprobante->igv,
                $comprobante->total,
                $comprobante->fecha_emision->format('Y-m-d'),
                $venta->cliente ? ($venta->cliente->tipo_documento === 'RUC' ? '6' : '1') : '1',
                $venta->cliente ? $venta->cliente->numero_documento : '00000000'
            );

            // Actualizar comprobante
            $comprobante->update([
                'xml_path' => $xmlPath,
                'cdr_path' => $cdrPath,
                'hash' => $resultado['hash'],
                'qr_data' => $qrData,
                'estado_sunat' => $resultado['success'] ? 'ACEPTADO' : 'RECHAZADO',
                'codigo_sunat' => $resultado['code'],
                'mensaje_sunat' => $resultado['message'],
                'fecha_envio_sunat' => now()
            ]);

            Log::info('Comprobante generado y enviado a SUNAT', [
                'comprobante_id' => $comprobante->id,
                'tipo' => $comprobante->tipo_comprobante,
                'serie_numero' => $comprobante->comprobante_completo,
                'estado' => $comprobante->estado_sunat
            ]);

        } catch (\Exception $e) {
            Log::error('Error al generar y enviar comprobante', [
                'comprobante_id' => $comprobante->id,
                'error' => $e->getMessage()
            ]);

            $comprobante->update([
                'estado_sunat' => 'RECHAZADO',
                'mensaje_sunat' => 'Error: ' . $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Construye una Factura para Greenter
     */
    protected function construirFactura(ComprobanteElectronico $comprobante, Venta $venta): Invoice
    {
        $invoice = new Invoice();
        
        // Datos del comprobante
        $invoice->setUblVersion('2.1')
            ->setTipoOperacion('0101') // Venta interna
            ->setTipoDoc('01') // Factura
            ->setSerie($comprobante->serie)
            ->setCorrelativo($comprobante->numero)
            ->setFechaEmision($comprobante->fecha_emision)
            ->setTipoMoneda('PEN');

        // Datos de la empresa
        $company = $this->construirEmpresa();
        $invoice->setCompany($company);

        // Datos del cliente
        $client = $this->construirCliente($venta);
        $invoice->setClient($client);

        // Detalles de la venta
        $items = $this->construirDetalles($venta);
        $invoice->setDetails($items);

        // Totales
        $invoice->setMtoOperGravadas($comprobante->subtotal)
            ->setMtoIGV($comprobante->igv)
            ->setTotalImpuestos($comprobante->igv)
            ->setValorVenta($comprobante->subtotal)
            ->setSubTotal($comprobante->total)
            ->setMtoImpVenta($comprobante->total);

        // Leyenda (monto en letras)
        $legend = new Legend();
        $legend->setCode('1000') // Monto en letras
            ->setValue($this->numeroALetras($comprobante->total));
        $invoice->setLegends([$legend]);

        return $invoice;
    }

    /**
     * Construye una Boleta para Greenter
     */
    protected function construirBoleta(ComprobanteElectronico $comprobante, Venta $venta): Invoice
    {
        $boleta = new Invoice();
        
        // Datos del comprobante
        $boleta->setUblVersion('2.1')
            ->setTipoOperacion('0101')
            ->setTipoDoc('03') // Boleta
            ->setSerie($comprobante->serie)
            ->setCorrelativo($comprobante->numero)
            ->setFechaEmision($comprobante->fecha_emision)
            ->setTipoMoneda('PEN');

        // Datos de la empresa
        $company = $this->construirEmpresa();
        $boleta->setCompany($company);

        // Datos del cliente (puede ser genérico)
        $client = $this->construirCliente($venta);
        $boleta->setClient($client);

        // Detalles
        $items = $this->construirDetalles($venta);
        $boleta->setDetails($items);

        // Totales
        $boleta->setMtoOperGravadas($comprobante->subtotal)
            ->setMtoIGV($comprobante->igv)
            ->setTotalImpuestos($comprobante->igv)
            ->setValorVenta($comprobante->subtotal)
            ->setSubTotal($comprobante->total)
            ->setMtoImpVenta($comprobante->total);

        // Leyenda
        $legend = new Legend();
        $legend->setCode('1000')
            ->setValue($this->numeroALetras($comprobante->total));
        $boleta->setLegends([$legend]);

        return $boleta;
    }

    /**
     * Construye el objeto Company de Greenter
     */
    protected function construirEmpresa(): Company
    {
        $config = config('greenter.company');
        
        $company = new Company();
        $company->setRuc($config['ruc'])
            ->setRazonSocial($config['razon_social'])
            ->setNombreComercial($config['nombre_comercial']);

        $address = new Address();
        $address->setUbigueo($config['address']['ubigeo'])
            ->setDepartamento($config['address']['departamento'])
            ->setProvincia($config['address']['provincia'])
            ->setDistrito($config['address']['distrito'])
            ->setUrbanizacion($config['address']['urbanizacion'])
            ->setDireccion($config['address']['direccion'])
            ->setCodLocal('0000'); // Código de establecimiento

        $company->setAddress($address);

        return $company;
    }

    /**
     * Construye el objeto Client de Greenter
     */
    protected function construirCliente(Venta $venta): Client
    {
        $client = new Client();

        if ($venta->cliente) {
            // Cliente registrado
            $tipoDoc = $venta->cliente->tipo_documento === 'RUC' ? '6' : 
                      ($venta->cliente->tipo_documento === 'DNI' ? '1' : '0');
            
            $client->setTipoDoc($tipoDoc)
                ->setNumDoc($venta->cliente->numero_documento)
                ->setRznSocial($venta->cliente->nombre);

            if ($venta->cliente->direccion) {
                $address = new Address();
                $address->setDireccion($venta->cliente->direccion);
                $client->setAddress($address);
            }
        } elseif ($venta->nombre_cliente_generico) {
            // Cliente genérico con nombre
            $client->setTipoDoc('1') // DNI
                ->setNumDoc('00000000')
                ->setRznSocial($venta->nombre_cliente_generico);
        } else {
            // Cliente público general
            $client->setTipoDoc('1')
                ->setNumDoc('00000000')
                ->setRznSocial('CLIENTE GENERAL');
        }

        return $client;
    }

    /**
     * Construye los detalles (items) del comprobante
     */
    protected function construirDetalles(Venta $venta): array
    {
        $items = [];
        
        foreach ($venta->detalles as $index => $detalle) {
            $item = new SaleDetail();
            $item->setCodProducto($detalle->producto->codigo ?? 'P' . str_pad($detalle->producto_id, 6, '0', STR_PAD_LEFT))
                ->setUnidad('NIU') // Unidad de medida (NIU = Unidad)
                ->setCantidad($detalle->cantidad)
                ->setDescripcion($detalle->producto->nombre)
                ->setMtoBaseIgv($detalle->subtotal / 1.18) // Base imponible
                ->setPorcentajeIgv(18.00)
                ->setIgv($detalle->subtotal - ($detalle->subtotal / 1.18))
                ->setTipAfeIgv('10') // Gravado - Operación Onerosa
                ->setTotalImpuestos($detalle->subtotal - ($detalle->subtotal / 1.18))
                ->setMtoValorVenta($detalle->subtotal / 1.18)
                ->setMtoValorUnitario($detalle->precio_unitario / 1.18)
                ->setMtoPrecioUnitario($detalle->precio_unitario);

            $items[] = $item;
        }

        return $items;
    }

    /**
     * Obtiene la serie según el tipo de comprobante
     */
    protected function obtenerSerie(string $tipoComprobante): string
    {
        return match($tipoComprobante) {
            'FACTURA' => config('greenter.series.factura', 'F001'),
            'BOLETA' => config('greenter.series.boleta', 'B001'),
            'NOTA_VENTA' => 'NV01',
            'NOTA_CREDITO' => config('greenter.series.nota_credito', 'FC01'),
            'NOTA_DEBITO' => config('greenter.series.nota_debito', 'FD01'),
            default => 'X001'
        };
    }

    /**
     * Genera el número correlativo para un tipo de comprobante y serie
     */
    protected function generarNumeroCorrelativo(string $tipoComprobante, string $serie): string
    {
        $ultimoNumero = ComprobanteElectronico::where('tipo_comprobante', $tipoComprobante)
            ->where('serie', $serie)
            ->max('numero');

        $siguiente = $ultimoNumero ? ((int) $ultimoNumero + 1) : 1;

        return str_pad($siguiente, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Convierte un número a letras (para la leyenda)
     * Implementación simplificada - puedes usar una librería más completa
     */
    protected function numeroALetras(float $numero): string
    {
        $entero = floor($numero);
        $decimales = round(($numero - $entero) * 100);

        // Implementación básica - considera usar una librería como luecano/numero-a-letras
        return strtoupper("SON " . $this->convertirNumeroALetras($entero) . " CON $decimales/100 SOLES");
    }

    /**
     * Convierte número entero a letras (implementación básica)
     */
    protected function convertirNumeroALetras(int $numero): string
    {
        // Implementación muy básica - solo para demostración
        // Recomiendo usar una librería completa como luecano/numero-a-letras
        if ($numero == 0) return "CERO";
        if ($numero < 10) {
            $unidades = ["", "UNO", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE"];
            return $unidades[$numero];
        }
        // Para números mayores, usar librería externa
        return (string) $numero;
    }

    /**
     * Reenvía un comprobante a SUNAT
     */
    public function reenviarASunat(ComprobanteElectronico $comprobante): void
    {
        if (!$comprobante->es_electronico) {
            throw new \Exception('Este comprobante no es electrónico');
        }

        if (!$comprobante->puedeEnviarse()) {
            throw new \Exception('Este comprobante no puede ser reenviado');
        }

        $venta = $comprobante->venta;
        $this->generarYEnviarASunat($comprobante, $venta);
    }

    /**
     * Consulta el estado de un comprobante en SUNAT
     */
    public function consultarEstadoSunat(ComprobanteElectronico $comprobante): array
    {
        if (!$comprobante->es_electronico) {
            return [
                'success' => false,
                'message' => 'Este comprobante no es electrónico'
            ];
        }

        return $this->greenterService->consultarEstado(
            config('greenter.company.ruc'),
            $comprobante->getCodigoTipoSunat(),
            $comprobante->serie,
            $comprobante->numero
        );
    }
}
