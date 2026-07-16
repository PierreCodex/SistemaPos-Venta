<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo DetalleCompra - Líneas de productos en una compra
 */
class DetalleCompra extends Model
{
    protected $table = 'detalle_compras';
    
    public $timestamps = false;

    protected $fillable = [
        'compra_id',
        'producto_id',
        'presentacion_id',
        'cantidad',
        'factor_aplicado',
        'cantidad_base',
        'costo_unitario',
        'descuento',
        'subtotal',
        'fecha_vencimiento',
        'lote'
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'factor_aplicado' => 'decimal:4',
        'cantidad_base' => 'decimal:3',
        'costo_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'fecha_vencimiento' => 'date'
    ];

    // ==================== RELACIONES ====================

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Presentación en la que se compró esta línea
     */
    public function presentacion(): BelongsTo
    {
        return $this->belongsTo(ProductoPresentacion::class, 'presentacion_id');
    }
}
