<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaGlobal extends Model
{
    protected $table = 'categorias_globales';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];

    // Relación: Una categoría global tiene muchas subcategorías
    public function categorias()
    {
        return $this->hasMany(Categoria::class, 'categoria_global_id');
    }
}
