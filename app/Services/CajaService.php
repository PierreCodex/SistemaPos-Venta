<?php

namespace App\Services;

use App\Models\CajaSesion;
use App\Models\CajaMovimiento;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Servicio para la lógica de negocio de Caja.
 * 
 * Maneja apertura, cierre, movimientos de caja
 * y validaciones para ventas.
 * 
 * @package App\Services
 */
class CajaService
{
    /**
     * Roles que pueden ver todas las sesiones de caja
     */
    protected array $rolesAdministrativos = ['super-admin', 'administrador', 'Admin'];

    /**
     * Verifica si el usuario actual tiene rol administrativo
     * (puede ver todas las sesiones de caja)
     */
    public function esAdministrador(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        return $user->hasAnyRole($this->rolesAdministrativos);
    }

    /**
     * Aplica filtro de usuario a una query si no es administrador
     */
    protected function aplicarFiltroUsuario($query)
    {
        if (!$this->esAdministrador()) {
            $query->where('user_id', Auth::id());
        }
        
        return $query;
    }

    /**
     * Obtiene la caja abierta actual (si existe)
     * Para usuarios normales: solo su propia caja
     * Para administradores: cualquier caja abierta
     */
    public function getCajaAbierta(): ?CajaSesion
    {
        $query = CajaSesion::abierta()
            ->with(['usuario', 'movimientos']);

        $this->aplicarFiltroUsuario($query);

        return $query->first();
    }

    /**
     * Verifica si existe una caja abierta
     */
    public function existeCajaAbierta(): bool
    {
        $query = CajaSesion::abierta();
        $this->aplicarFiltroUsuario($query);
        return $query->exists();
    }

    /**
     * Obtiene el ID de la caja abierta actual
     */
    public function getCajaAbiertaId(): ?int
    {
        $query = CajaSesion::abierta();
        $this->aplicarFiltroUsuario($query);
        return $query->value('id');
    }

    /**
     * Abre una nueva sesión de caja
     * 
     * @param float $montoInicial Efectivo inicial
     * @param string|null $observaciones Observaciones de apertura
     * @return CajaSesion
     * @throws \Exception Si ya hay una caja abierta
     */
    public function abrirCaja(float $montoInicial, ?string $observaciones = null): CajaSesion
    {
        // Verificar que no haya caja abierta
        if ($this->existeCajaAbierta()) {
            throw new \Exception('Ya existe una caja abierta. Debe cerrarla antes de abrir una nueva.');
        }

        return DB::transaction(function () use ($montoInicial, $observaciones) {
            $cajaSesion = CajaSesion::create([
                'user_id' => Auth::id(),
                'monto_inicial' => $montoInicial,
                'fecha_apertura' => now(),
                'observaciones' => $observaciones,
                'estado' => CajaSesion::ESTADO_ABIERTA,
                'total_ventas' => 0,
                'total_ingresos' => 0,
                'total_egresos' => 0,
            ]);

            // El monto inicial NO se registra como movimiento de ingreso
            // porque ya se cuenta en el cálculo de monto_actual
            // Solo se registran como ingresos las ventas y depósitos durante la sesión

            return $cajaSesion->fresh(['usuario']);
        });
    }

    /**
     * Cierra la sesión de caja actual
     * 
     * @param float $montoFisico Efectivo físico contado
     * @param string|null $observaciones Observaciones de cierre
     * @return CajaSesion
     * @throws \Exception Si no hay caja abierta
     */
    public function cerrarCaja(float $montoFisico, ?string $observaciones = null): CajaSesion
    {
        $cajaSesion = $this->getCajaAbierta();

        if (!$cajaSesion) {
            throw new \Exception('No hay una caja abierta para cerrar.');
        }

        return DB::transaction(function () use ($cajaSesion, $montoFisico, $observaciones) {
            // Recalcular totales antes de cerrar
            $cajaSesion->recalcularTotales();
            
            // Calcular monto esperado y diferencia
            $montoEsperado = floatval($cajaSesion->monto_inicial) 
                + floatval($cajaSesion->total_ingresos) 
                - floatval($cajaSesion->total_egresos);
            
            $diferencia = $montoFisico - $montoEsperado;

            // Actualizar sesión
            $cajaSesion->update([
                'user_cierre_id' => Auth::id(),
                'monto_final' => $montoFisico,
                'monto_esperado' => $montoEsperado,
                'diferencia' => $diferencia,
                'fecha_cierre' => now(),
                'estado' => CajaSesion::ESTADO_CERRADA,
                'observaciones' => $cajaSesion->observaciones 
                    ? $cajaSesion->observaciones . "\n\n--- CIERRE ---\n" . $observaciones
                    : $observaciones,
            ]);

            return $cajaSesion->fresh(['usuario', 'usuarioCierre', 'movimientos']);
        });
    }

    /**
     * Registra un movimiento manual en la caja
     * 
     * @param string $tipo INGRESO o EGRESO
     * @param string $concepto Concepto del movimiento
     * @param float $monto Monto del movimiento
     * @param string $descripcion Descripción del movimiento
     * @return CajaMovimiento
     * @throws \Exception Si no hay caja abierta
     */
    public function registrarMovimiento(
        string $tipo,
        string $concepto,
        float $monto,
        string $descripcion,
        ?string $referenciaTipo = null,
        ?int $referenciaId = null
    ): CajaMovimiento {
        $cajaSesion = $this->getCajaAbierta();

        if (!$cajaSesion) {
            throw new \Exception('No hay una caja abierta. Debe abrir caja antes de registrar movimientos.');
        }

        return DB::transaction(function () use ($cajaSesion, $tipo, $concepto, $monto, $descripcion, $referenciaTipo, $referenciaId) {
            $movimiento = CajaMovimiento::create([
                'caja_sesion_id' => $cajaSesion->id,
                'user_id' => Auth::id(),
                'tipo' => $tipo,
                'concepto' => $concepto,
                'monto' => $monto,
                'descripcion' => $descripcion,
                'referencia_tipo' => $referenciaTipo,
                'referencia_id' => $referenciaId,
            ]);

            // Actualizar totales en la sesión
            if ($tipo === CajaMovimiento::TIPO_INGRESO) {
                $cajaSesion->increment('total_ingresos', $monto);
            } else {
                $cajaSesion->increment('total_egresos', $monto);
            }

            return $movimiento;
        });
    }

    /**
     * Registra automáticamente una venta en la caja
     * 
     * @param Venta $venta La venta a registrar
     * @return CajaMovimiento|null El movimiento creado o null si no es en efectivo
     */
    public function registrarVenta(Venta $venta): ?CajaMovimiento
    {
        // Solo registrar si hay pago en efectivo
        $montoEfectivo = floatval($venta->pago_efectivo ?? 0);
        
        if ($montoEfectivo <= 0) {
            return null;
        }

        $cajaSesion = $this->getCajaAbierta();
        
        if (!$cajaSesion) {
            return null;
        }

        return DB::transaction(function () use ($cajaSesion, $venta, $montoEfectivo) {
            $movimiento = CajaMovimiento::create([
                'caja_sesion_id' => $cajaSesion->id,
                'user_id' => Auth::id(),
                'tipo' => CajaMovimiento::TIPO_INGRESO,
                'concepto' => CajaMovimiento::CONCEPTO_VENTA,
                'monto' => $montoEfectivo,
                'descripcion' => "Venta #{$venta->id} - {$venta->comprobante_completo}",
                'referencia_tipo' => 'ventas',
                'referencia_id' => $venta->id,
            ]);

            // Actualizar totales
            $cajaSesion->increment('total_ventas', $venta->total);
            $cajaSesion->increment('total_ingresos', $montoEfectivo);

            return $movimiento;
        });
    }

    /**
     * Registra automáticamente una anulación de venta
     */
    public function registrarAnulacionVenta(Venta $venta): ?CajaMovimiento
    {
        $montoEfectivo = floatval($venta->pago_efectivo ?? 0);
        
        if ($montoEfectivo <= 0) {
            return null;
        }

        $cajaSesion = $this->getCajaAbierta();
        
        if (!$cajaSesion) {
            return null;
        }

        return $this->registrarMovimiento(
            CajaMovimiento::TIPO_EGRESO,
            CajaMovimiento::CONCEPTO_OTRO,
            $montoEfectivo,
            "Anulación Venta #{$venta->id} - {$venta->comprobante_completo}",
            'ventas',
            $venta->id
        );
    }

    /**
     * Obtiene el resumen de la caja actual para cierre
     */
    public function getResumenCaja(?int $sesionId = null): array
    {
        $cajaSesion = $sesionId 
            ? CajaSesion::with(['movimientos', 'ventas', 'usuario', 'usuarioCierre'])->findOrFail($sesionId)
            : $this->getCajaAbierta();

        if (!$cajaSesion) {
            return [];
        }

        // Recalcular totales
        $cajaSesion->recalcularTotales();

        $montoEsperado = floatval($cajaSesion->monto_inicial) 
            + floatval($cajaSesion->total_ingresos) 
            - floatval($cajaSesion->total_egresos);

        // Desglose por concepto
        $ingresosPorConcepto = $cajaSesion->movimientos()
            ->ingresos()
            ->selectRaw('concepto, SUM(monto) as total')
            ->groupBy('concepto')
            ->pluck('total', 'concepto')
            ->toArray();

        $egresosPorConcepto = $cajaSesion->movimientos()
            ->egresos()
            ->selectRaw('concepto, SUM(monto) as total')
            ->groupBy('concepto')
            ->pluck('total', 'concepto')
            ->toArray();

        // Ventas por método de pago
        $ventasPorMetodo = $cajaSesion->ventas()
            ->where('estado', 'COMPLETADA')
            ->selectRaw('metodo_pago, COUNT(*) as cantidad, SUM(total) as total')
            ->groupBy('metodo_pago')
            ->get()
            ->keyBy('metodo_pago')
            ->toArray();

        return [
            'sesion' => $cajaSesion,
            'monto_inicial' => floatval($cajaSesion->monto_inicial),
            'total_ventas' => floatval($cajaSesion->total_ventas),
            'total_ingresos' => floatval($cajaSesion->total_ingresos),
            'total_egresos' => floatval($cajaSesion->total_egresos),
            'monto_esperado' => $montoEsperado,
            'cantidad_ventas' => $cajaSesion->ventas()->where('estado', 'COMPLETADA')->count(),
            'cantidad_movimientos' => $cajaSesion->movimientos()->count(),
            'ingresos_por_concepto' => $ingresosPorConcepto,
            'egresos_por_concepto' => $egresosPorConcepto,
            'ventas_por_metodo' => $ventasPorMetodo,
            'duracion' => $cajaSesion->duracion,
        ];
    }

    /**
     * Obtiene el historial de sesiones de caja
     * Filtrado por usuario según rol
     */
    public function getHistorial(int $limit = 30, ?string $fechaInicio = null, ?string $fechaFin = null)
    {
        $query = CajaSesion::with(['usuario', 'usuarioCierre'])
            ->orderBy('fecha_apertura', 'desc');

        // Aplicar filtro de usuario si no es administrador
        $this->aplicarFiltroUsuario($query);

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha_apertura', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ]);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Valida que exista una caja abierta antes de procesar una venta
     * @throws \Exception Si no hay caja abierta
     */
    public function validarCajaParaVenta(): void
    {
        if (!$this->existeCajaAbierta()) {
            throw new \Exception('Debe abrir la caja antes de realizar ventas. Vaya a Caja → Abrir Caja.');
        }
    }

    /**
     * Obtiene estadísticas de caja por período
     * Filtrado por usuario según rol
     */
    public function getEstadisticas(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $query = CajaSesion::cerrada();

        // Aplicar filtro de usuario si no es administrador
        $this->aplicarFiltroUsuario($query);

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha_apertura', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ]);
        } else {
            // Por defecto, último mes
            $query->where('fecha_apertura', '>=', now()->subMonth());
        }

        $sesiones = $query->get();

        return [
            'cantidad_sesiones' => $sesiones->count(),
            'total_ventas' => $sesiones->sum('total_ventas'),
            'total_ingresos' => $sesiones->sum('total_ingresos'),
            'total_egresos' => $sesiones->sum('total_egresos'),
            'diferencia_total' => $sesiones->sum('diferencia'),
            'promedio_ventas_por_sesion' => $sesiones->count() > 0 
                ? $sesiones->sum('total_ventas') / $sesiones->count() 
                : 0,
            'sesiones_con_faltante' => $sesiones->where('diferencia', '<', 0)->count(),
            'sesiones_con_sobrante' => $sesiones->where('diferencia', '>', 0)->count(),
            'sesiones_cuadradas' => $sesiones->where('diferencia', '=', 0)->count(),
        ];
    }

    /**
     * Obtiene los pagos de crédito recibidos en una sesión de caja
     * 
     * @param int|null $sesionId ID de la sesión (null = caja actual)
     * @return array
     */
    public function getPagosCreditoSesion(?int $sesionId = null): array
    {
        $cajaSesion = $sesionId 
            ? CajaSesion::findOrFail($sesionId) 
            : $this->getCajaAbierta();

        if (!$cajaSesion) {
            return [
                'pagos' => collect(),
                'totales' => [
                    'efectivo' => 0,
                    'otros' => 0,
                    'total' => 0,
                    'cantidad' => 0,
                ]
            ];
        }

        $pagos = \App\Models\VentaCreditoPago::with(['venta.cliente', 'user'])
            ->where('caja_sesion_id', $cajaSesion->id)
            ->orderBy('fecha_pago', 'desc')
            ->get();

        $totales = [
            'efectivo' => $pagos->where('metodo_pago', 'EFECTIVO')->sum('monto'),
            'otros' => $pagos->where('metodo_pago', '!=', 'EFECTIVO')->sum('monto'),
            'total' => $pagos->sum('monto'),
            'cantidad' => $pagos->count(),
        ];

        return [
            'pagos' => $pagos,
            'totales' => $totales,
            'cajaSesion' => $cajaSesion,
        ];
    }
}
