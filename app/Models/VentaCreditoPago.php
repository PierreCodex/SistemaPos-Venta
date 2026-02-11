<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaCreditoPago extends Model
{
    protected $table = 'venta_credito_pagos';

    protected $fillable = [
        'venta_id',
        'monto',
        'metodo_pago',
        'fecha_pago',
        'numero_operacion',
        'user_id',
        'caja_sesion_id',
        'observaciones'
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto' => 'decimal:2'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cajaSesion()
    {
        return $this->belongsTo(CajaSesion::class, 'caja_sesion_id');
    }
}
