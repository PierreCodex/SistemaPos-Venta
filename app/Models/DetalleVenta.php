<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo DetalleVenta - Líneas de productos de una venta
 * 
 * Cada fila representa un producto vendido con su cantidad,
 * precio y descuento aplicado en esa venta específica.
 */
class DetalleVenta extends Model
{
    protected $table = 'detalle_ventas';

    protected $fillable = [
        'venta_id',
        'producto_id',
        'presentacion_id',
        'cantidad',
        'factor_aplicado',
        'cantidad_base',
        'precio_unitario',
        'precio_original',
        'descuento',
        'subtotal'
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'factor_aplicado' => 'decimal:4',
        'cantidad_base' => 'decimal:3',
        'precio_unitario' => 'decimal:2',
        'precio_original' => 'decimal:2',
        'descuento' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // =====================================================
    // 🔗 RELACIONES
    // =====================================================

    /**
     * Venta a la que pertenece este detalle
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    /**
     * Producto vendido
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Presentación en la que se vendió esta línea
     */
    public function presentacion()
    {
        return $this->belongsTo(ProductoPresentacion::class, 'presentacion_id');
    }

    // =====================================================
    // 🔧 ACCESSORS & HELPERS
    // =====================================================

    /**
     * Calcula el subtotal de esta línea
     */
    public function calcularSubtotal(): float
    {
        return ($this->cantidad * $this->precio_unitario) - $this->descuento;
    }

    /**
     * Nombre del producto (para evitar queries)
     */
    public function getNombreProductoAttribute(): string
    {
        return $this->producto ? $this->producto->nombre : 'Producto eliminado';
    }
}
