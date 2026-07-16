<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    protected $table = 'kardex';
    public $timestamps = false;


    protected $fillable = [
        'producto_id',
        'presentacion_id',
        'tipo_movimiento',
        'referencia_tipo',
        'referencia_id',
        'cantidad',
        'cantidad_presentacion',
        'costo_unitario',
        'stock_anterior',
        'stock_resultante',
        'user_id',
        'observaciones',
        'created_at'
    ];

    // Las columnas de stock son DECIMAL(12,3) en la base: castearlas a
    // decimal:2 truncaba la tercera cifra al exponerlas (relevante para
    // gramos y mililitros).
    protected $casts = [
        'cantidad' => 'decimal:3',
        'cantidad_presentacion' => 'decimal:3',
        'costo_unitario' => 'decimal:2',
        'stock_anterior' => 'decimal:3',
        'stock_resultante' => 'decimal:3',
        'created_at' => 'datetime',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Presentación del movimiento. Solo para mostrar "2 cajas" en vez
     * de "48": cantidad siempre está en unidad base.
     */
    public function presentacion()
    {
        return $this->belongsTo(ProductoPresentacion::class, 'presentacion_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
