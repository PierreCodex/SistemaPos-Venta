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
}