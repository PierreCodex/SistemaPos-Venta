<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo Compra - Registro de ingreso de mercadería
 */
class Compra extends Model
{
    protected $table = 'compras';

    protected $fillable = [
        'proveedor_id',
        'user_id',
        'tipo_comprobante',
        'numero_comprobante',
        'fecha_emision',
        'fecha_vencimiento',
        'subtotal',
        'igv',
        'descuento',
        'total',
        'forma_pago',
        'estado_pago',
        'monto_pagado',
        'observaciones',
        'estado'
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'subtotal' => 'decimal:2',
        'igv' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
    ];

    // ==================== RELACIONES ====================

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleCompra::class);
    }

    // ==================== ACCESSORS ====================

    public function getSaldoPendienteAttribute(): float
    {
        return $this->total - $this->monto_pagado;
    }

    public function getEstaVencidoAttribute(): bool
    {
        if (!$this->fecha_vencimiento) return false;
        return $this->fecha_vencimiento->isPast() && $this->estado_pago !== 'PAGADO';
    }

    // ==================== SCOPES ====================

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'COMPLETADO');
    }

    public function scopePendientesPago($query)
    {
        return $query->whereIn('estado_pago', ['PENDIENTE', 'PARCIAL']);
    }

    public function scopeDelMes($query, $mes = null, $anio = null)
    {
        $mes = $mes ?? now()->month;
        $anio = $anio ?? now()->year;
        
        return $query->whereMonth('fecha_emision', $mes)
                     ->whereYear('fecha_emision', $anio);
    }
}
