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
}
