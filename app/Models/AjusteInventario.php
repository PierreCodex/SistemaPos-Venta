<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo AjusteInventario - Ajustes manuales de stock
 */
class AjusteInventario extends Model
{
    protected $table = 'ajustes_inventario';

    protected $fillable = [
        'tipo',
        'motivo',
        'descripcion',
        'user_id',
        'fecha',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'datetime'
    ];

    // ==================== CONSTANTES ====================

    const TIPO_ENTRADA = 'ENTRADA';
    const TIPO_SALIDA = 'SALIDA';
    const TIPO_CONTEO = 'CONTEO';

    const MOTIVO_MERMA = 'MERMA';
    const MOTIVO_ROBO = 'ROBO';
    const MOTIVO_VENCIMIENTO = 'VENCIMIENTO';
    const MOTIVO_ERROR_CONTEO = 'ERROR_CONTEO';
    const MOTIVO_DONACION = 'DONACION';
    const MOTIVO_OTRO = 'OTRO';

    const ESTADO_BORRADOR = 'BORRADOR';
    const ESTADO_APLICADO = 'APLICADO';
    const ESTADO_ANULADO = 'ANULADO';

    // ==================== RELACIONES ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleAjusteInventario::class, 'ajuste_id');
    }

    // ==================== ACCESSORS ====================

    public function getTotalProductosAttribute(): int
    {
        return $this->detalles()->count();
    }

    public function getTotalDiferenciaAttribute(): float
    {
        return $this->detalles()->sum('diferencia');
    }

    // ==================== SCOPES ====================

    public function scopeAplicados($query)
    {
        return $query->where('estado', self::ESTADO_APLICADO);
    }

    public function scopeBorradores($query)
    {
        return $query->where('estado', self::ESTADO_BORRADOR);
    }

    // ==================== HELPERS ====================

    public static function getTipos(): array
    {
        return [
            self::TIPO_ENTRADA => 'Entrada',
            self::TIPO_SALIDA => 'Salida',
            self::TIPO_CONTEO => 'Conteo Físico'
        ];
    }

    public static function getMotivos(): array
    {
        return [
            self::MOTIVO_MERMA => 'Merma',
            self::MOTIVO_ROBO => 'Robo / Pérdida',
            self::MOTIVO_VENCIMIENTO => 'Vencimiento',
            self::MOTIVO_ERROR_CONTEO => 'Error de Conteo',
            self::MOTIVO_DONACION => 'Donación',
            self::MOTIVO_OTRO => 'Otro'
        ];
    }
}
