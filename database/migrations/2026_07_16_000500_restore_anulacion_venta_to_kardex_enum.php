<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * =====================================================
 * Restaura ANULACION_VENTA en el enum de kardex
 * =====================================================
 *
 * BUG PREEXISTENTE, no relacionado con las presentaciones.
 *
 * 2026_01_29_180000_add_anulacion_venta_to_kardex agregó
 * 'ANULACION_VENTA' al enum. Dos días después,
 * 2026_01_31_190000_update_kardex_tipos_movimiento redefinió el enum
 * COMPLETO para sumar AJUSTE_ENTRADA/AJUSTE_SALIDA/CANCELACION, pero
 * omitió ANULACION_VENTA en la lista. Al ser un MODIFY que reescribe
 * el enum entero, borró el valor que la migración anterior había puesto.
 *
 * Consecuencia: VentaService::anular() escribe 'ANULACION_VENTA' en el
 * kardex, así que anular cualquier venta falla con "Data truncated for
 * column 'tipo_movimiento'" y la transacción entera se revierte.
 *
 * Se restaura el valor conservando todos los demás (unión, no reemplazo)
 * para no romper filas ya existentes.
 *
 * =====================================================
 */
return new class extends Migration
{
    /**
     * Todos los valores en uso: los que el código escribe hoy más los que
     * ya vivían en el enum.
     */
    private const VALORES = [
        'VENTA',
        'COMPRA',
        'DEVOLUCION_CLIENTE',
        'DEVOLUCION_PROVEEDOR',
        'INVENTARIO_INICIAL',
        'AJUSTE_POSITIVO',
        'AJUSTE_NEGATIVO',
        'AJUSTE_ENTRADA',
        'AJUSTE_SALIDA',
        'TRANSFERENCIA',
        'MERMA',
        'CANCELACION',
        'ANULACION_VENTA',
    ];

    public function up(): void
    {
        $enum = "'" . implode("','", self::VALORES) . "'";

        DB::statement("ALTER TABLE kardex MODIFY tipo_movimiento ENUM({$enum}) NOT NULL");
    }

    public function down(): void
    {
        // Vuelve al enum tal como lo dejó 2026_01_31_190000 (sin ANULACION_VENTA).
        // Si ya existen movimientos de anulación, se quedarían fuera del enum:
        // por eso se bloquea el rollback en ese caso en vez de corromperlos.
        $anulaciones = DB::table('kardex')->where('tipo_movimiento', 'ANULACION_VENTA')->count();

        if ($anulaciones > 0) {
            throw new \RuntimeException(
                "No se puede revertir: hay {$anulaciones} movimientos ANULACION_VENTA en el kardex que quedarían fuera del enum."
            );
        }

        $valores = array_diff(self::VALORES, ['ANULACION_VENTA']);
        $enum = "'" . implode("','", $valores) . "'";

        DB::statement("ALTER TABLE kardex MODIFY tipo_movimiento ENUM({$enum}) NOT NULL");
    }
};
