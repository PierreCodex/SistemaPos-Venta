<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo CajaSesion - Sesión/Turno de Caja
 * 
 * Representa una sesión de caja desde su apertura hasta el cierre.
 * Incluye montos iniciales, finales, totales de ventas y movimientos.
 */
class CajaSesion extends Model
{
    protected $table = 'caja_sesiones';

    protected $fillable = [
        'user_id',
        'user_cierre_id',
        'monto_inicial',
        'monto_final',
        'monto_esperado',
        'diferencia',
        'total_ventas',
        'total_ingresos',
        'total_egresos',
        'fecha_apertura',
        'fecha_cierre',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_inicial' => 'decimal:2',
        'monto_final' => 'decimal:2',
        'monto_esperado' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'total_ventas' => 'decimal:2',
        'total_ingresos' => 'decimal:2',
        'total_egresos' => 'decimal:2',
    ];

    // =====================================================
    // CONSTANTES
    // =====================================================

    const ESTADO_ABIERTA = 'ABIERTA';
    const ESTADO_CERRADA = 'CERRADA';
    const ESTADO_ARQUEADA = 'ARQUEADA';

    // =====================================================
    // 🔗 RELACIONES
    // =====================================================

    /**
     * Usuario que abrió la caja
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Usuario que cerró la caja (puede ser diferente)
     */
    public function usuarioCierre(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_cierre_id');
    }

    /**
     * Movimientos de caja (ingresos/egresos)
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(CajaMovimiento::class, 'caja_sesion_id');
    }

    /**
     * Ventas asociadas a esta sesión de caja
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'caja_sesion_id');
    }

    // =====================================================
    // 🎯 SCOPES
    // =====================================================

    /**
     * Sesiones abiertas
     */
    public function scopeAbierta($query)
    {
        return $query->where('estado', self::ESTADO_ABIERTA);
    }

    /**
     * Sesiones cerradas
     */
    public function scopeCerrada($query)
    {
        return $query->where('estado', self::ESTADO_CERRADA);
    }

    /**
     * Sesiones de un usuario específico
     */
    public function scopeDeUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Sesiones de hoy
     */
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_apertura', today());
    }

    /**
     * Sesiones en un rango de fechas
     */
    public function scopeEntreFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('fecha_apertura', [$inicio, $fin]);
    }

    // =====================================================
    // 🔧 ACCESSORS & HELPERS
    // =====================================================

    /**
     * Verifica si la caja está abierta
     */
    public function estaAbierta(): bool
    {
        return $this->estado === self::ESTADO_ABIERTA;
    }

    /**
     * Verifica si la caja está cerrada
     */
    public function estaCerrada(): bool
    {
        return $this->estado === self::ESTADO_CERRADA;
    }

    /**
     * Obtiene el monto actual en caja (calculado)
     */
    public function getMontoActualAttribute(): float
    {
        return floatval($this->monto_inicial) 
            + floatval($this->total_ingresos) 
            - floatval($this->total_egresos);
    }

    /**
     * Obtiene la duración de la sesión
     */
    public function getDuracionAttribute(): ?string
    {
        if (!$this->fecha_cierre) {
            return $this->fecha_apertura->diffForHumans(now(), true);
        }
        return $this->fecha_apertura->diffForHumans($this->fecha_cierre, true);
    }

    /**
     * Badge de estado para la vista
     */
    public function getBadgeEstadoAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_ABIERTA => '<span class="badge bg-success">Abierta</span>',
            self::ESTADO_CERRADA => '<span class="badge bg-secondary">Cerrada</span>',
            self::ESTADO_ARQUEADA => '<span class="badge bg-info">Arqueada</span>',
            default => '<span class="badge bg-dark">-</span>'
        };
    }

    /**
     * Badge de diferencia para la vista
     */
    public function getBadgeDiferenciaAttribute(): string
    {
        if ($this->diferencia === null) {
            return '';
        }
        
        if ($this->diferencia > 0) {
            return '<span class="badge bg-success">+' . number_format($this->diferencia, 2) . ' Sobrante</span>';
        } elseif ($this->diferencia < 0) {
            return '<span class="badge bg-danger">' . number_format($this->diferencia, 2) . ' Faltante</span>';
        }
        
        return '<span class="badge bg-info">Cuadrado</span>';
    }

    /**
     * Recalcula los totales de la sesión
     */
    public function recalcularTotales(): void
    {
        $this->total_ventas = $this->ventas()
            ->where('estado', 'COMPLETADA')
            ->sum('total');

        $this->total_ingresos = $this->movimientos()
            ->where('tipo', CajaMovimiento::TIPO_INGRESO)
            ->sum('monto');

        $this->total_egresos = $this->movimientos()
            ->where('tipo', CajaMovimiento::TIPO_EGRESO)
            ->sum('monto');

        $this->monto_esperado = floatval($this->monto_inicial) 
            + floatval($this->total_ingresos) 
            - floatval($this->total_egresos);

        $this->save();
    }
}
