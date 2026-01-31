<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    //
    protected $table = 'marcas';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'estado',
    ];

    // Relación: Una marca tiene muchos productos
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }


}
