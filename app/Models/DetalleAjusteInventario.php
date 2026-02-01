<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo DetalleAjusteInventario - Líneas de productos en un ajuste
 */
class DetalleAjusteInventario extends Model
{
    protected $table = 'detalle_ajustes_inventario';
    
    public $timestamps = false;

    protected $fillable = [
        'ajuste_id',
        'producto_id',
        'stock_sistema',
        'stock_fisico',
        'diferencia',
        'observacion'
    ];

    protected $casts = [
        'stock_sistema' => 'decimal:3',
        'stock_fisico' => 'decimal:3',
        'diferencia' => 'decimal:3'
    ];

    // ==================== RELACIONES ====================

    public function ajuste(): BelongsTo
    {
        return $this->belongsTo(AjusteInventario::class, 'ajuste_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    // ==================== ACCESSORS ====================

    public function getTipoDiferenciaAttribute(): string
    {
        if ($this->diferencia > 0) return 'sobrante';
        if ($this->diferencia < 0) return 'faltante';
        return 'exacto';
    }
}
