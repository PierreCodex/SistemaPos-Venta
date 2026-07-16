<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    // Usamos SoftDeletes porque tu tabla tiene deleted_at para no perder historial contable
    use SoftDeletes; 

    protected $table = 'productos';
    
    protected $fillable = [
        'codigo',
        'codigo_barras',
        'nombre',
        'categoria_id',
        'marca_id',
        'unidad_id',
        'proveedor_id',
        'precio_compra',
        'precio_venta',
        'precio_mayorista',
        'cantidad_mayorista',
        'aplica_igv',
        'es_servicio',
        'permite_venta_negativa',
        'stock',
        'stock_minimo',
        'stock_maximo',
        'descripcion',
        'imagen',
        'imagen_url',
        'ubicacion',
        'material',
        'fecha_vencimiento',
        'estado'
    ];

    // =====================================================
    // 🔗 RELACIONES: El producto PERTENECE A (BelongsTo)
    // =====================================================

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'marca_id');
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'unidad_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    // =====================================================
    // 🔗 RELACIONES: El producto TIENE MUCHOS (HasMany)
    // =====================================================

    public function kardex()
    {
        return $this->hasMany(Kardex::class, 'producto_id');
    }

    public function historialPrecios()
    {
        return $this->hasMany(ProductoHistorialPrecio::class, 'producto_id');
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'producto_id');
    }

    /**
     * Formas en que se puede vender este producto (unidad, caja x24...)
     */
    public function presentaciones()
    {
        return $this->hasMany(ProductoPresentacion::class, 'producto_id');
    }

    /**
     * La presentación en la que se lleva el stock (factor 1).
     */
    public function presentacionBase()
    {
        return $this->hasOne(ProductoPresentacion::class, 'producto_id')
                    ->where('es_base', true);
    }

    /**
     * Presentaciones en el formato que consume el POS.
     *
     * El POS necesita el factor para poder validar stock en unidad base
     * sin volver al servidor en cada clic.
     */
    public function presentacionesParaPos(): array
    {
        return $this->presentaciones
            ->map(fn (ProductoPresentacion $p) => [
                'id'         => $p->id,
                'unidad'     => $p->unidad->codigo ?? '',
                'nombre'     => $p->unidad->nombre ?? '',
                'factor'     => (float) $p->factor,
                'precio'     => (float) $p->precio_venta,
                'decimales'  => $p->permiteDecimales() ? 1 : 0,
                'esBase'     => $p->es_base ? 1 : 0,
            ])
            ->values()
            ->all();
    }

    /**
     * Resuelve la presentación a usar en una operación de stock.
     *
     * Sin presentacion_id devuelve la base (factor 1), que reproduce
     * exactamente el comportamiento previo a las presentaciones: así el
     * POS y la API que aún no envían el campo siguen funcionando igual.
     *
     * Verifica que la presentación PERTENEZCA a este producto: sin esta
     * comprobación, un cliente podría enviar el id de la presentación
     * "Caja x24" de otro producto y descontar stock a un factor ajeno.
     *
     * @throws \Exception si la presentación no existe, no es de este producto o está inactiva
     */
    public function resolverPresentacion(?int $presentacionId = null): ProductoPresentacion
    {
        if ($presentacionId === null) {
            $base = $this->presentacionBase()->with('unidad')->first();

            if (!$base) {
                throw new \Exception("El producto '{$this->nombre}' no tiene una presentación base configurada. No se puede operar su stock.");
            }

            return $base;
        }

        $presentacion = $this->presentaciones()->with('unidad')->find($presentacionId);

        if (!$presentacion) {
            throw new \Exception("La presentación seleccionada no pertenece al producto '{$this->nombre}'.");
        }

        if (!$presentacion->estado) {
            throw new \Exception("La presentación '{$presentacion->unidad?->nombre}' de '{$this->nombre}' está desactivada.");
        }

        return $presentacion;
    }
}