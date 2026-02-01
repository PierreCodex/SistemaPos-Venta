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
        'observaciones',
        'created_at'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'costo_unitario' => 'decimal:2',
        'stock_anterior' => 'decimal:2',
        'stock_resultante' => 'decimal:2',
        'created_at' => 'datetime',
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
