<?php

namespace App\Services;

use App\Models\Venta;
use App\Models\ComprobanteElectronico;
use App\Models\Empresa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Luecano\NumeroALetras\NumeroALetras;

/**
 * Servicio para generar PDFs de comprobantes electrónicos
 */
class PdfComprobanteService
{
    /**
     * Genera PDFs en todos los formatos (50mm, 80mm, A4)
     * 
     * @param ComprobanteElectronico $comprobante
     * @return array ['50mm' => path, '80mm' => path, 'a4' => path]
     */
    public function generarTodosLosFormatos(ComprobanteElectronico $comprobante): array
    {
        $paths = [];
        
        // Generar PDF 50mm
        $paths['50mm'] = $this->generarPdf($comprobante, '50mm');
        
        // Generar PDF 80mm
        $paths['80mm'] = $this->generarPdf($comprobante, '80mm');
        
        // Generar PDF A4
        $paths['a4'] = $this->generarPdf($comprobante, 'a4');
        
        // Actualizar el comprobante con la ruta del PDF principal (80mm por defecto)
        $comprobante->update([
            'pdf_path' => $paths['80mm']
        ]);
        
        return $paths;
    }

    /**
     * Genera un PDF en un formato específico
     * 
     * @param ComprobanteElectronico $comprobante
     * @param string $formato '50mm', '80mm', 'a4', 'a5'
     * @return string Ruta relativa del PDF generado
     */
    public function generarPdf(ComprobanteElectronico $comprobante, string $formato = '80mm'): string
    {
        $venta = $comprobante->venta;
        
        // Preparar datos para la vista
        $data = $this->prepararDatos($comprobante, $venta);
        
        // Determinar la vista según el tipo de comprobante
        $vista = $this->obtenerVista($comprobante->tipo_comprobante, $formato);
        
        // Configurar tamaño del papel según formato
        $paperSize = $this->obtenerTamanoPapel($formato);
        
        // Generar PDF
        $pdf = Pdf::loadView($vista, $data)
            ->setPaper($paperSize, 'portrait');
        
        // Guardar PDF
        $filename = $this->generarNombreArchivo($comprobante, $formato);
        $path = "greenter/pdf/{$formato}/{$filename}";
        
        Storage::put($path, $pdf->output());
        
        return $path;
    }

    /**
     * Prepara los datos para la vista del PDF
     */
    protected function prepararDatos(ComprobanteElectronico $comprobante, Venta $venta): array
    {
        // Datos de la empresa desde la base de datos
        $empresa = Empresa::principal();
        $company = [
            'ruc' => $empresa->ruc ?? config('greenter.company.ruc'),
            'razon_social' => $empresa->razon_social ?? config('greenter.company.razon_social'),
            'nombre_comercial' => $empresa->nombre_comercial ?? config('greenter.company.nombre_comercial'),
            'direccion' => $empresa->direccion ?? config('greenter.company.address.direccion'),
            'telefono' => $empresa->telefono,
            'email' => $empresa->email,
            'website' => $empresa->website ?? null,
            'ubigeo' => $empresa->ubigeo ?? null,
            'departamento' => $empresa->departamento ?? null,
            'provincia' => $empresa->provincia ?? null,
            'distrito' => $empresa->distrito ?? null,
        ];

        // Datos del documento
        $document = [
            'serie' => $comprobante->serie,
            'numero' => $comprobante->numero,
            'correlativo' => $comprobante->comprobante_completo,
            'tipo_comprobante' => $comprobante->tipo_comprobante,
        ];

        // Datos del cliente
        $client = [
            'nombre' => $venta->nombre_cliente ?? 'CLIENTE GENERAL',
            'tipo_documento' => $venta->cliente?->tipo_documento ?? 'DNI',
            'numero_documento' => $venta->cliente?->numero_documento ?? '00000000',
            'direccion' => $venta->cliente?->direccion ?? '-',
        ];

        // Detalles de la venta con formateo de cantidades
        $detalles = $venta->detalles->map(function ($detalle) {
            $unidad = $detalle->producto->unidad->codigo ?? 'NIU';
            return [
                'cantidad' => $detalle->cantidad,
                'unidad' => $unidad,
                'descripcion' => $detalle->producto->nombre,
                'precio_unitario' => $detalle->precio_unitario,
                'subtotal' => $detalle->subtotal,
                // Formateo condicional para vista
                'cantidad_formateada' => in_array($unidad, ['KG', 'LTR']) 
                    ? number_format($detalle->cantidad, 3) 
                    : number_format($detalle->cantidad, 0),
            ];
        })->toArray();

        // Totales
        $totales = [
            'subtotal' => $comprobante->subtotal,
            'igv' => $comprobante->igv,
            'total' => $comprobante->total,
        ];

        // Generar código QR si es electrónico
        $qrCode = null;
        if ($comprobante->es_electronico && $comprobante->qr_data) {
            try {
                // Usar GD como backend (viene por defecto con PHP)
                $qrCode = 'data:image/png;base64,' . base64_encode(
                    QrCode::format('png')
                        ->size(150)
                        ->errorCorrection('H')
                        ->generate($comprobante->qr_data)
                );
            } catch (\Exception $e) {
                \Log::error('Error generando QR code', ['error' => $e->getMessage()]);
                $qrCode = null; // Continuar sin QR si falla
            }
        }

        // Nombre del tipo de documento
        $tipoDocumentoNombre = $this->obtenerNombreTipoDocumento($comprobante);

        // Total en letras
        $totalEnLetras = $this->numeroALetras($comprobante->total);

        return [
            'company' => $company,
            'document' => $document,
            'client' => $client,
            'detalles' => $detalles,
            'totales' => $totales,
            'qr_code' => $qrCode,
            'hash' => $comprobante->hash,
            'tipo_documento_nombre' => $tipoDocumentoNombre,
            'fecha_emision' => $comprobante->fecha_emision->format('d/m/Y'),
            'total_en_letras' => $totalEnLetras,
            'es_electronico' => $comprobante->es_electronico,
            'metodo_pago' => $venta->metodo_pago,
            'monto_recibido' => $venta->monto_recibido,
            'vuelto' => $venta->vuelto,
        ];
    }

    /**
     * Obtiene la vista según el tipo de comprobante y formato
     */
    protected function obtenerVista(string $tipoComprobante, string $formato): string
    {
        $vistas = [
            'FACTURA' => 'pdf.' . $formato . '.invoice',
            'BOLETA' => 'pdf.' . $formato . '.boleta',
            'NOTA_VENTA' => 'pdf.' . $formato . '.boleta', // Usar misma plantilla que boleta
            'NOTA_CREDITO' => 'pdf.' . $formato . '.credit-note',
            'NOTA_DEBITO' => 'pdf.' . $formato . '.debit-note',
        ];

        return $vistas[$tipoComprobante] ?? 'pdf.' . $formato . '.boleta';
    }

    /**
     * Obtiene el tamaño del papel según el formato
     */
    protected function obtenerTamanoPapel(string $formato): array|string
    {
        return match($formato) {
            '50mm' => [0, 0, 141.73, 1000], // 50mm de ancho, altura variable
            '80mm' => [0, 0, 226.77, 1000], // 80mm de ancho, altura variable
            'a4' => 'a4',
            'a5' => 'a5',
            default => [0, 0, 226.77, 1000]
        };
    }

    /**
     * Genera el nombre del archivo PDF
     */
    protected function generarNombreArchivo(ComprobanteElectronico $comprobante, string $formato): string
    {
        return $comprobante->serie . '-' . $comprobante->numero . '-' . $formato . '.pdf';
    }

    /**
     * Obtiene el nombre legible del tipo de documento
     */
    protected function obtenerNombreTipoDocumento(ComprobanteElectronico $comprobante): string
    {
        if (!$comprobante->es_electronico) {
            return 'NOTA DE VENTA';
        }

        return match($comprobante->tipo_comprobante) {
            'FACTURA' => 'FACTURA ELECTRÓNICA',
            'BOLETA' => 'BOLETA ELECTRÓNICA',
            'NOTA_CREDITO' => 'NOTA DE CRÉDITO ELECTRÓNICA',
            'NOTA_DEBITO' => 'NOTA DE DÉBITO ELECTRÓNICA',
            default => $comprobante->tipo_comprobante
        };
    }

    /**
     * Convierte número a letras usando librería luecano/numero-a-letras
     */
    protected function numeroALetras(float $numero): string
    {
        $formatter = new NumeroALetras();
        $entero = floor($numero);
        $decimales = round(($numero - $entero) * 100);
        
        $letras = $formatter->toWords($entero);
        $decimalesFormateado = str_pad($decimales, 2, '0', STR_PAD_LEFT);
        
        return strtoupper($letras) . " CON {$decimalesFormateado}/100 SOLES";
    }

    /**
     * Obtiene la URL pública para descargar un PDF
     */
    public function obtenerUrlDescarga(string $path): string
    {
        return route('comprobantes.descargar-pdf', ['path' => base64_encode($path)]);
    }
}
