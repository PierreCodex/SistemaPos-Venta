<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    
    protected $fillable = [
        'categoria_global_id',
        'nombre',
        'descripcion',
        'estado'
    ];

    // Relación: Una categoría pertenece a una categoría global
    public function categoriaGlobal()
    {
        return $this->belongsTo(CategoriaGlobal::class, 'categoria_global_id');
    }

    // Relación: Una categoría tiene muchos productos
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
