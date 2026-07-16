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
    // 🛡️ REGLAS DE INTEGRIDAD
    // =====================================================

    /**
     * Se aplican en el modelo y no en un servicio a propósito: así valen
     * para cualquier vía de entrada (controlador, seeder, tinker), no solo
     * para quien pase por la capa de servicio.
     */
    protected static function booted(): void
    {
        static::saving(function (ProductoPresentacion $presentacion) {
            $presentacion->validarFactorDeBase();
            $presentacion->validarFactorInmutable();
            $presentacion->validarBaseUnica();
        });

        static::deleting(function (ProductoPresentacion $presentacion) {
            if ($presentacion->tieneMovimientos()) {
                throw new \Exception(
                    "No se puede eliminar esta presentación porque tiene ventas o compras registradas. Desactívela en su lugar."
                );
            }

            if ($presentacion->es_base) {
                throw new \Exception(
                    "No se puede eliminar la presentación base: es la unidad en la que se lleva el stock del producto."
                );
            }
        });
    }

    /**
     * La presentación base define la unidad del stock, así que su factor
     * es 1 por definición.
     */
    private function validarFactorDeBase(): void
    {
        if ($this->es_base && (float) $this->factor !== 1.0) {
            throw new \Exception('La presentación base debe tener factor 1: es la unidad en la que se mide el stock.');
        }
    }

    /**
     * Cambiar el factor de una presentación ya usada reescribiría el
     * significado del histórico: las ventas viejas guardan factor_aplicado,
     * pero el stock actual se calculó con el factor anterior y no cuadraría.
     *
     * Lo correcto es desactivar esta presentación y crear una nueva.
     */
    private function validarFactorInmutable(): void
    {
        if (!$this->exists || !$this->isDirty('factor')) {
            return;
        }

        if ($this->tieneMovimientos()) {
            $anterior = $this->getOriginal('factor');

            throw new \Exception(
                "No se puede cambiar el factor de {$anterior} a {$this->factor}: esta presentación ya tiene ventas o compras registradas. Desactívela y cree una presentación nueva."
            );
        }
    }

    /**
     * Un producto lleva su stock en UNA sola unidad base.
     * MySQL no tiene índices únicos parciales, así que se valida aquí.
     */
    private function validarBaseUnica(): void
    {
        if (!$this->es_base) {
            return;
        }

        $otraBase = static::where('producto_id', $this->producto_id)
            ->where('es_base', true)
            ->when($this->exists, fn ($q) => $q->where('id', '!=', $this->id))
            ->exists();

        if ($otraBase) {
            throw new \Exception('El producto ya tiene una presentación base. Solo puede haber una.');
        }
    }

    /**
     * Si esta presentación ya fue usada en una venta o una compra
     */
    public function tieneMovimientos(): bool
    {
        return DetalleVenta::where('presentacion_id', $this->id)->exists()
            || DetalleCompra::where('presentacion_id', $this->id)->exists();
    }

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
