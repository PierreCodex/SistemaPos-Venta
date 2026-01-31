<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo ComprobanteElectronico
 * 
 * Representa un comprobante electrónico (Factura, Boleta, Nota de Crédito/Débito)
 * o una Nota de Venta (no electrónica).
 * 
 * @property int $id
 * @property int $venta_id
 * @property string $tipo_comprobante
 * @property string $serie
 * @property string $numero
 * @property \DateTime $fecha_emision
 * @property string $moneda
 * @property string $tipo_operacion
 * @property float $subtotal
 * @property float $igv
 * @property float $total
 * @property string|null $xml_path
 * @property string|null $pdf_path
 * @property string|null $cdr_path
 * @property string|null $hash
 * @property string|null $qr_data
 * @property string $estado_sunat
 * @property string|null $codigo_sunat
 * @property string|null $mensaje_sunat
 * @property \DateTime|null $fecha_envio_sunat
 * @property bool $es_electronico
 * @property string|null $observaciones
 */
class ComprobanteElectronico extends Model
{
    protected $table = 'comprobantes_electronicos';

    protected $fillable = [
        'venta_id',
        'tipo_comprobante',
        'serie',
        'numero',
        'fecha_emision',
        'moneda',
        'tipo_operacion',
        'subtotal',
        'igv',
        'total',
        'xml_path',
        'pdf_path',
        'cdr_path',
        'hash',
        'qr_data',
        'estado_sunat',
        'codigo_sunat',
        'mensaje_sunat',
        'fecha_envio_sunat',
        'es_electronico',
        'observaciones'
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'fecha_envio_sunat' => 'datetime',
        'es_electronico' => 'boolean',
        'subtotal' => 'decimal:2',
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // =====================================================
    // 🔗 RELACIONES
    // =====================================================

    /**
     * Venta asociada al comprobante
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    // =====================================================
    // 🎯 SCOPES
    // =====================================================

    /**
     * Filtra solo comprobantes electrónicos (enviados a SUNAT)
     */
    public function scopeElectronicos($query)
    {
        return $query->where('es_electronico', true);
    }

    /**
     * Filtra solo notas de venta (no electrónicas)
     */
    public function scopeNotasVenta($query)
    {
        return $query->where('es_electronico', false)
                     ->where('tipo_comprobante', 'NOTA_VENTA');
    }

    /**
     * Filtra comprobantes aceptados por SUNAT
     */
    public function scopeAceptados($query)
    {
        return $query->where('estado_sunat', 'ACEPTADO');
    }

    /**
     * Filtra comprobantes pendientes de envío
     */
    public function scopePendientes($query)
    {
        return $query->where('estado_sunat', 'PENDIENTE')
                     ->where('es_electronico', true);
    }

    /**
     * Filtra comprobantes rechazados por SUNAT
     */
    public function scopeRechazados($query)
    {
        return $query->where('estado_sunat', 'RECHAZADO');
    }

    /**
     * Filtra por tipo de comprobante
     */
    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo_comprobante', $tipo);
    }

    /**
     * Filtra por rango de fechas
     */
    public function scopeEntreFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('fecha_emision', [$inicio, $fin]);
    }

    // =====================================================
    // 🔧 ACCESSORS & HELPERS
    // =====================================================

    /**
     * Número completo del comprobante (Serie-Número)
     * Ejemplo: F001-00000123
     */
    public function getComprobanteCompletoAttribute(): string
    {
        return $this->serie . '-' . str_pad($this->numero, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Nombre legible del tipo de comprobante
     */
    public function getTipoComprobanteNombreAttribute(): string
    {
        if (!$this->es_electronico) {
            return 'NOTA DE VENTA';
        }

        return match($this->tipo_comprobante) {
            'FACTURA' => 'FACTURA ELECTRÓNICA',
            'BOLETA' => 'BOLETA ELECTRÓNICA',
            'NOTA_CREDITO' => 'NOTA DE CRÉDITO ELECTRÓNICA',
            'NOTA_DEBITO' => 'NOTA DE DÉBITO ELECTRÓNICA',
            'NOTA_VENTA' => 'NOTA DE VENTA',
            default => $this->tipo_comprobante
        };
    }

    /**
     * Badge de estado para la vista
     */
    public function getBadgeEstadoAttribute(): string
    {
        return match($this->estado_sunat) {
            'ACEPTADO' => '<span class="badge bg-success">Aceptado</span>',
            'RECHAZADO' => '<span class="badge bg-danger">Rechazado</span>',
            'PENDIENTE' => '<span class="badge bg-warning">Pendiente</span>',
            'ANULADO' => '<span class="badge bg-secondary">Anulado</span>',
            'NO_APLICA' => '<span class="badge bg-info">No Aplica</span>',
            default => '<span class="badge bg-secondary">-</span>'
        };
    }

    /**
     * Verifica si el comprobante fue aceptado por SUNAT
     */
    public function estaAceptado(): bool
    {
        return $this->estado_sunat === 'ACEPTADO';
    }

    /**
     * Verifica si el comprobante está pendiente de envío
     */
    public function estaPendiente(): bool
    {
        return $this->estado_sunat === 'PENDIENTE' && $this->es_electronico;
    }

    /**
     * Verifica si el comprobante puede ser enviado a SUNAT
     */
    public function puedeEnviarse(): bool
    {
        return $this->es_electronico && 
               in_array($this->estado_sunat, ['PENDIENTE', 'RECHAZADO']);
    }

    /**
     * Verifica si tiene XML generado
     */
    public function tieneXml(): bool
    {
        return !empty($this->xml_path) && file_exists(storage_path('app/' . $this->xml_path));
    }

    /**
     * Verifica si tiene PDF generado
     */
    public function tienePdf(): bool
    {
        return !empty($this->pdf_path) && file_exists(storage_path('app/' . $this->pdf_path));
    }

    /**
     * Verifica si tiene CDR (Constancia de Recepción)
     */
    public function tieneCdr(): bool
    {
        return !empty($this->cdr_path) && file_exists(storage_path('app/' . $this->cdr_path));
    }

    /**
     * Obtiene el código de tipo de comprobante para SUNAT
     * 01 = Factura, 03 = Boleta, 07 = Nota Crédito, 08 = Nota Débito
     */
    public function getCodigoTipoSunat(): string
    {
        return match($this->tipo_comprobante) {
            'FACTURA' => '01',
            'BOLETA' => '03',
            'NOTA_CREDITO' => '07',
            'NOTA_DEBITO' => '08',
            default => ''
        };
    }
}
