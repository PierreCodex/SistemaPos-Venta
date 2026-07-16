<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo ProductoPresentacion - Formas de venta de un producto
 *
 * Una presentación es "este producto, en esta unidad, equivale a
 * N unidades base". Es lo que permite vender el mismo producto
 * suelto y por caja contra un único inventario.
 *
 * El stock del producto siempre se lleva en unidad base
 * (la presentación con es_base = true, factor 1).
 */
class ProductoPresentacion extends Model
{
    protected $table = 'producto_presentaciones';

    protected $fillable = [
        'producto_id',
        'unidad_id',
        'factor',
        'precio_venta',
        'codigo_barras',
        'es_base',
        'estado',
    ];

    protected $casts = [
        'factor' => 'decimal:4',
        'precio_venta' => 'decimal:2',
        'es_base' => 'boolean',
        'estado' => 'boolean',
    ];

    // =====================================================
    // 🔗 RELACIONES
    // =====================================================

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class, 'unidad_id');
    }

    // =====================================================
    // 🔧 CONVERSIÓN
    // =====================================================

    /**
     * Convierte una cantidad expresada en esta presentación
     * a unidades base. Es el único lugar donde debe ocurrir
     * esta multiplicación.
     */
    public function aBase(float $cantidad): float
    {
        return round($cantidad * (float) $this->factor, 3);
    }

    /**
     * Convierte una cantidad en unidades base a esta presentación.
     * Se usa para mostrar stock disponible ("quedan 2 cajas").
     */
    public function desdeBase(float $cantidadBase): float
    {
        $factor = (float) $this->factor;

        return $factor > 0 ? round($cantidadBase / $factor, 3) : 0.0;
    }

    /**
     * Si la unidad de esta presentación admite decimales.
     * Se consulta contra la unidad de la PRESENTACIÓN, no la del producto.
     */
    public function permiteDecimales(): bool
    {
        return (bool) ($this->unidad->permite_decimales ?? false);
    }

    // =====================================================
    // 🔍 SCOPES
    // =====================================================

    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    public function scopeBase($query)
    {
        return $query->where('es_base', true);
    }
}
