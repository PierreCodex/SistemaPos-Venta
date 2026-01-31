<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    protected $table = 'kardex';

    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'tipo_movimiento',
        'referencia_tipo',
        'referencia_id',
        'cantidad',
        'costo_unitario',
        'stock_anterior',
        'stock_resultante',
        'user_id',
        'observaciones'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
