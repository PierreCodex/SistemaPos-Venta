<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo Venta - Cabecera de venta
 * 
 * Representa una venta completa con sus totales,
 * cliente, vendedor y método de pago.
 */
class Venta extends Model
{
    use SoftDeletes;

    protected $table = 'ventas';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($venta) {
            $venta->codigo_externo = self::generarCodigoUnico();
        });
    }

    private static function generarCodigoUnico()
    {
        do {
            $codigo = strtoupper(\Illuminate\Support\Str::random(10));
        } while (self::where('codigo_externo', $codigo)->exists());

        return $codigo;
    }

    protected $fillable = [
        'codigo_externo',
        'caja_sesion_id',
        'cliente_id',
        'nombre_cliente_generico',
        'user_id',
        'comprobante',
        'serie',
        'numero',
        'metodo_pago',
        'igv_porcentaje',
        'subtotal',
        'descuento',
        'igv_monto',
        'total',
        'monto_recibido',
        'vuelto',
        'pago_efectivo',
        'pago_tarjeta',
        'pago_yape',
        'pago_plin',
        'pago_transferencia',
        'es_credito',
        'fecha_vencimiento_credito',
        'estado_pago',
        'saldo_pendiente',
        'fecha_emision',
        'fecha_vencimiento',
        'estado',
        'observaciones',
        'motivo_anulacion',
        'fecha_anulacion',
        'user_anulacion_id'
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'fecha_vencimiento' => 'date',
        'fecha_vencimiento_credito' => 'date',
        'fecha_anulacion' => 'datetime',
        'es_credito' => 'boolean',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'igv_monto' => 'decimal:2',
        'total' => 'decimal:2',
        'monto_recibido' => 'decimal:2',
        'vuelto' => 'decimal:2',
    ];

    // =====================================================
    // 🔗 RELACIONES
    // =====================================================

    /**
     * Cliente de la venta (puede ser null para ventas a público general)
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Vendedor que realizó la venta
     */
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Usuario que anuló la venta
     */
    public function usuarioAnulacion()
    {
        return $this->belongsTo(User::class, 'user_anulacion_id');
    }

    /**
     * Sesión de caja en la que se realizó esta venta
     */
    public function cajaSesion()
    {
        return $this->belongsTo(CajaSesion::class, 'caja_sesion_id');
    }

    /**
     * Detalles de la venta (productos vendidos)
     */
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    /**
     * Comprobante electrónico asociado a la venta
     */
    public function comprobanteElectronico()
    {
        return $this->hasOne(ComprobanteElectronico::class, 'venta_id');
    }

    /**
     * Pagos/Abonos realizados a esta venta (crédito)
     */
    public function pagos()
    {
        return $this->hasMany(VentaCreditoPago::class, 'venta_id');
    }

    // =====================================================
    // 🎯 SCOPES
    // =====================================================

    /**
     * Ventas completadas
     */
    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'COMPLETADA');
    }

    /**
     * Ventas anuladas
     */
    public function scopeAnuladas($query)
    {
        return $query->where('estado', 'ANULADA');
    }

    /**
     * Ventas pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'PENDIENTE');
    }

    /**
     * Ventas del día actual
     */
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_emision', today());
    }

    /**
     * Ventas en un rango de fechas
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
     */
    public function getComprobanteCompletoAttribute(): string
    {
        return $this->serie . '-' . str_pad($this->numero, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Nombre del cliente o "Cliente General"
     */
    public function getNombreClienteAttribute(): string
    {
        // Prioridad: Cliente registrado -> Nombre genérico -> Cliente General
        if ($this->cliente) {
            return $this->cliente->nombre;
        }
        
        return $this->nombre_cliente_generico ?: 'Cliente General';
    }

    /**
     * URL pública para compartir por WhatsApp
     */
    public function getUrlPublicaAttribute(): string
    {
        if (!$this->codigo_externo) {
            return '#';
        }
        return route('ticket.publico', ['codigo' => $this->codigo_externo]);
    }

    /**
     * Badge de estado para la vista
     */
    public function getBadgeEstadoAttribute(): string
    {
        return match($this->estado) {
            'COMPLETADA' => '<span class="badge bg-success">Completada</span>',
            'ANULADA' => '<span class="badge bg-danger">Anulada</span>',
            'PENDIENTE' => '<span class="badge bg-warning">Pendiente</span>',
            default => '<span class="badge bg-secondary">-</span>'
        };
    }
}
