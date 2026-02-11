<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo CajaMovimiento - Movimientos de Caja
 * 
 * Representa un ingreso o egreso de efectivo en la caja.
 * Puede estar vinculado a ventas, compras, gastos u otros conceptos.
 */
class CajaMovimiento extends Model
{
    protected $table = 'caja_movimientos';

    /**
     * Solo tiene created_at, no updated_at
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'caja_sesion_id',
        'user_id',
        'tipo',
        'concepto',
        'monto',
        'descripcion',
        'referencia_tipo',
        'referencia_id',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // =====================================================
    // CONSTANTES - TIPOS
    // =====================================================

    const TIPO_INGRESO = 'INGRESO';
    const TIPO_EGRESO = 'EGRESO';

    // =====================================================
    // CONSTANTES - CONCEPTOS
    // =====================================================

    const CONCEPTO_VENTA = 'VENTA';
    const CONCEPTO_COMPRA = 'COMPRA';
    const CONCEPTO_PAGO_PROVEEDOR = 'PAGO_PROVEEDOR';
    const CONCEPTO_GASTO = 'GASTO';
    const CONCEPTO_DEPOSITO = 'DEPOSITO';
    const CONCEPTO_RETIRO = 'RETIRO';
    const CONCEPTO_PAGO_CLIENTE = 'PAGO_CLIENTE';
    const CONCEPTO_OTRO = 'OTRO';

    /**
     * Lista de conceptos disponibles
     */
    public static function conceptos(): array
    {
        return [
            self::CONCEPTO_VENTA => 'Venta',
            self::CONCEPTO_COMPRA => 'Compra',
            self::CONCEPTO_PAGO_PROVEEDOR => 'Pago a Proveedor',
            self::CONCEPTO_GASTO => 'Gasto',
            self::CONCEPTO_DEPOSITO => 'Depósito',
            self::CONCEPTO_RETIRO => 'Retiro',
            self::CONCEPTO_PAGO_CLIENTE => 'Pago de Cliente',
            self::CONCEPTO_OTRO => 'Otro',
        ];
    }

    /**
     * Conceptos para ingresos
     */
    public static function conceptosIngreso(): array
    {
        return [
            self::CONCEPTO_VENTA => 'Venta',
            self::CONCEPTO_DEPOSITO => 'Depósito',
            self::CONCEPTO_PAGO_CLIENTE => 'Pago de Cliente',
            self::CONCEPTO_OTRO => 'Otro Ingreso',
        ];
    }

    /**
     * Conceptos para egresos
     */
    public static function conceptosEgreso(): array
    {
        return [
            self::CONCEPTO_COMPRA => 'Compra',
            self::CONCEPTO_PAGO_PROVEEDOR => 'Pago a Proveedor',
            self::CONCEPTO_GASTO => 'Gasto',
            self::CONCEPTO_RETIRO => 'Retiro',
            self::CONCEPTO_OTRO => 'Otro Egreso',
        ];
    }

    // =====================================================
    // 🔗 RELACIONES
    // =====================================================

    /**
     * Sesión de caja a la que pertenece
     */
    public function cajaSesion(): BelongsTo
    {
        return $this->belongsTo(CajaSesion::class, 'caja_sesion_id');
    }

    /**
     * Usuario que registró el movimiento
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // =====================================================
    // 🎯 SCOPES
    // =====================================================

    /**
     * Solo ingresos
     */
    public function scopeIngresos($query)
    {
        return $query->where('tipo', self::TIPO_INGRESO);
    }

    /**
     * Solo egresos
     */
    public function scopeEgresos($query)
    {
        return $query->where('tipo', self::TIPO_EGRESO);
    }

    /**
     * Por concepto
     */
    public function scopeConcepto($query, string $concepto)
    {
        return $query->where('concepto', $concepto);
    }

    /**
     * De una sesión específica
     */
    public function scopeDeSesion($query, int $sesionId)
    {
        return $query->where('caja_sesion_id', $sesionId);
    }

    // =====================================================
    // 🔧 ACCESSORS & HELPERS
    // =====================================================

    /**
     * Verifica si es un ingreso
     */
    public function esIngreso(): bool
    {
        return $this->tipo === self::TIPO_INGRESO;
    }

    /**
     * Verifica si es un egreso
     */
    public function esEgreso(): bool
    {
        return $this->tipo === self::TIPO_EGRESO;
    }

    /**
     * Descripción del concepto legible
     */
    public function getConceptoTextoAttribute(): string
    {
        return self::conceptos()[$this->concepto] ?? $this->concepto;
    }

    /**
     * Badge de tipo para la vista
     */
    public function getBadgeTipoAttribute(): string
    {
        return match($this->tipo) {
            self::TIPO_INGRESO => '<span class="badge bg-success">Ingreso</span>',
            self::TIPO_EGRESO => '<span class="badge bg-danger">Egreso</span>',
            default => '<span class="badge bg-secondary">-</span>'
        };
    }

    /**
     * Monto formateado con signo
     */
    public function getMontoFormateadoAttribute(): string
    {
        $signo = $this->esIngreso() ? '+' : '-';
        $clase = $this->esIngreso() ? 'text-success' : 'text-danger';
        return "<span class=\"{$clase}\">{$signo} S/ " . number_format($this->monto, 2) . "</span>";
    }
}
