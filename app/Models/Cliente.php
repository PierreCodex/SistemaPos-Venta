<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Cliente
 * 
 * Representa un cliente del negocio (compradores).
 */
class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'tipo_documento',
        'numero_documento',
        'nombre',
        'telefono',
        'email',
        'direccion',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];

    // =====================================================
    // 🔗 RELACIONES
    // =====================================================

    /**
     * Ventas realizadas por este cliente
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cliente_id');
    }

    // =====================================================
    // 🔧 ACCESSORS
    // =====================================================

    /**
     * Documento completo (Tipo + Número)
     */
    public function getDocumentoCompletoAttribute(): string
    {
        return $this->tipo_documento . ': ' . $this->numero_documento;
    }

    // =====================================================
    // 🎯 SCOPES
    // =====================================================

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}
