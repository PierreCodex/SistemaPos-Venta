<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    protected $table = 'unidades';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'permite_decimales',
        'estado',
    ];

    /**
     * Relación: Una unidad tiene muchos productos
     */
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    /**
     * Relación: Una unidad se usa en muchas presentaciones
     *
     * Una unidad puede no estar en ningún producto y aun así estar en uso
     * (ej: "Caja" como presentación de un producto cuya base es "Unidad").
     */
    public function presentaciones()
    {
        return $this->hasMany(ProductoPresentacion::class, 'unidad_id');
    }
}
