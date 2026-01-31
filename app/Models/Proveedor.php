<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'tipo_documento',
        'documento',
        'nombre',
        'telefono',
        'email',
        'direccion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    // Relación: Un proveedor tiene muchas compras
    public function compras()
    {
        return $this->hasMany(Compra::class);
    }

    // Scope para proveedores activos
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}
