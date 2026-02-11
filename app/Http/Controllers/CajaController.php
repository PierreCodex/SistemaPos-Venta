<?php

namespace App\Http\Controllers;

use App\Services\CajaService;
use App\Models\CajaSesion;
use App\Models\CajaMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para gestión de Caja
 * 
 * Maneja apertura, cierre, movimientos y reportes de caja.
 */
class CajaController extends Controller
{
    protected CajaService $cajaService;

    public function __construct(CajaService $cajaService)
    {
        $this->cajaService = $cajaService;
    }

    /**
     * Dashboard de caja - Estado actual e historial
     */
    public function index()
    {
        $cajaAbierta = $this->cajaService->getCajaAbierta();
        $resumen = $cajaAbierta ? $this->cajaService->getResumenCaja() : [];
        $historial = $this->cajaService->getHistorial(15);

        return view('caja.index', compact('cajaAbierta', 'resumen', 'historial'));
    }

    /**
     * Formulario para abrir caja
     */
    public function apertura()
    {
        // Verificar si ya hay caja abierta
        if ($this->cajaService->existeCajaAbierta()) {
            return redirect()->route('caja.index')
                ->with('warning', 'Ya existe una caja abierta.');
        }

        return view('caja.apertura');
    }

    /**
     * Procesar apertura de caja
     */
    public function abrirCaja(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:500',
        ], [
            'monto_inicial.required' => 'Debe ingresar el monto inicial.',
            'monto_inicial.numeric' => 'El monto debe ser un número válido.',
            'monto_inicial.min' => 'El monto no puede ser negativo.',
        ]);

        try {
            $cajaSesion = $this->cajaService->abrirCaja(
                $request->monto_inicial,
                $request->observaciones
            );

            Log::info('Caja abierta', [
                'sesion_id' => $cajaSesion->id,
                'user_id' => auth()->id(),
                'monto_inicial' => $request->monto_inicial,
            ]);

            return redirect()->route('caja.index')
                ->with('success', '¡Caja abierta correctamente! Monto inicial: S/ ' . number_format($request->monto_inicial, 2));

        } catch (\Exception $e) {
            Log::error('Error al abrir caja', ['error' => $e->getMessage()]);
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Formulario para cerrar caja (arqueo)
     */
    public function cierre()
    {
        $cajaAbierta = $this->cajaService->getCajaAbierta();

        if (!$cajaAbierta) {
            return redirect()->route('caja.index')
                ->with('warning', 'No hay una caja abierta para cerrar.');
        }

        $resumen = $this->cajaService->getResumenCaja();

        return view('caja.cierre', compact('cajaAbierta', 'resumen'));
    }

    /**
     * Procesar cierre de caja
     */
    public function cerrarCaja(Request $request)
    {
        $request->validate([
            'monto_fisico' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:500',
        ], [
            'monto_fisico.required' => 'Debe ingresar el monto físico contado.',
            'monto_fisico.numeric' => 'El monto debe ser un número válido.',
            'monto_fisico.min' => 'El monto no puede ser negativo.',
        ]);

        try {
            $cajaSesion = $this->cajaService->cerrarCaja(
                $request->monto_fisico,
                $request->observaciones
            );

            $mensaje = '¡Caja cerrada correctamente!';
            if ($cajaSesion->diferencia > 0) {
                $mensaje .= ' Sobrante: S/ ' . number_format($cajaSesion->diferencia, 2);
            } elseif ($cajaSesion->diferencia < 0) {
                $mensaje .= ' Faltante: S/ ' . number_format(abs($cajaSesion->diferencia), 2);
            } else {
                $mensaje .= ' La caja cuadra perfectamente.';
            }

            Log::info('Caja cerrada', [
                'sesion_id' => $cajaSesion->id,
                'user_id' => auth()->id(),
                'monto_fisico' => $request->monto_fisico,
                'diferencia' => $cajaSesion->diferencia,
            ]);

            return redirect()->route('caja.show', $cajaSesion->id)
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            Log::error('Error al cerrar caja', ['error' => $e->getMessage()]);
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Ver detalle de una sesión de caja
     * Verifica que el usuario tenga acceso a esta sesión
     */
    public function show(int $id)
    {
        $cajaSesion = CajaSesion::with(['usuario', 'usuarioCierre', 'movimientos.usuario', 'ventas'])
            ->findOrFail($id);

        // Verificar acceso: solo admin o dueño de la sesión
        if (!$this->cajaService->esAdministrador() && $cajaSesion->user_id !== auth()->id()) {
            abort(403, 'No tiene permiso para ver esta sesión de caja.');
        }

        $resumen = $this->cajaService->getResumenCaja($id);

        return view('caja.show', compact('cajaSesion', 'resumen'));
    }

    /**
     * Formulario para registrar movimiento manual
     */
    public function movimientoForm()
    {
        $cajaAbierta = $this->cajaService->getCajaAbierta();

        if (!$cajaAbierta) {
            return redirect()->route('caja.index')
                ->with('warning', 'Debe abrir la caja antes de registrar movimientos.');
        }

        $conceptosIngreso = CajaMovimiento::conceptosIngreso();
        $conceptosEgreso = CajaMovimiento::conceptosEgreso();

        return view('caja.movimiento', compact('cajaAbierta', 'conceptosIngreso', 'conceptosEgreso'));
    }

    /**
     * Procesar registro de movimiento
     */
    public function storeMovimiento(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:INGRESO,EGRESO',
            'concepto' => 'required|string',
            'monto' => 'required|numeric|gt:0',
            'descripcion' => 'required|string|max:255',
        ], [
            'tipo.required' => 'Seleccione el tipo de movimiento.',
            'concepto.required' => 'Seleccione el concepto.',
            'monto.required' => 'Ingrese el monto.',
            'monto.gt' => 'El monto debe ser mayor a 0.',
            'descripcion.required' => 'Ingrese una descripción.',
        ]);

        try {
            $movimiento = $this->cajaService->registrarMovimiento(
                $request->tipo,
                $request->concepto,
                $request->monto,
                $request->descripcion
            );

            $tipoTexto = $request->tipo === 'INGRESO' ? 'Ingreso' : 'Egreso';

            Log::info('Movimiento de caja registrado', [
                'movimiento_id' => $movimiento->id,
                'tipo' => $request->tipo,
                'monto' => $request->monto,
            ]);

            return redirect()->route('caja.index')
                ->with('success', "{$tipoTexto} de S/ " . number_format($request->monto, 2) . " registrado correctamente.");

        } catch (\Exception $e) {
            Log::error('Error al registrar movimiento', ['error' => $e->getMessage()]);
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Ver movimientos de una sesión
     * Verifica que el usuario tenga acceso a esta sesión
     */
    public function movimientos(int $id)
    {
        $cajaSesion = CajaSesion::with(['movimientos.usuario'])
            ->findOrFail($id);

        // Verificar acceso: solo admin o dueño de la sesión
        if (!$this->cajaService->esAdministrador() && $cajaSesion->user_id !== auth()->id()) {
            abort(403, 'No tiene permiso para ver los movimientos de esta sesión.');
        }

        $movimientos = $cajaSesion->movimientos()
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('caja.movimientos', compact('cajaSesion', 'movimientos'));
    }

    /**
     * Reporte de caja por fechas
     */
    public function reporte(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? now()->startOfMonth()->toDateString();
        $fechaFin = $request->fecha_fin ?? now()->toDateString();

        $historial = $this->cajaService->getHistorial(100, $fechaInicio, $fechaFin);
        $estadisticas = $this->cajaService->getEstadisticas($fechaInicio, $fechaFin);

        return view('caja.reporte', compact('historial', 'estadisticas', 'fechaInicio', 'fechaFin'));
    }

    // =========================================================================
    // API ENDPOINTS (para AJAX)
    // =========================================================================

    /**
     * Estado actual de la caja (JSON)
     */
    public function estadoJson()
    {
        $cajaAbierta = $this->cajaService->getCajaAbierta();
        
        if (!$cajaAbierta) {
            return response()->json([
                'abierta' => false,
                'mensaje' => 'No hay caja abierta',
            ]);
        }

        $resumen = $this->cajaService->getResumenCaja();

        return response()->json([
            'abierta' => true,
            'sesion_id' => $cajaAbierta->id,
            'monto_actual' => $resumen['monto_esperado'],
            'total_ventas' => $resumen['total_ventas'],
            'cantidad_ventas' => $resumen['cantidad_ventas'],
            'usuario' => $cajaAbierta->usuario->name ?? '',
            'fecha_apertura' => $cajaAbierta->fecha_apertura->format('d/m/Y H:i'),
            'duracion' => $cajaAbierta->duracion,
        ]);
    }

    /**
     * Verificar si hay caja abierta (para validación de ventas)
     */
    public function verificarCajaJson()
    {
        return response()->json([
            'caja_abierta' => $this->cajaService->existeCajaAbierta(),
            'caja_sesion_id' => $this->cajaService->getCajaAbiertaId(),
        ]);
    }

    /**
     * Muestra los pagos de crédito recibidos en una sesión de caja
     */
    public function pagosCredito(?int $id = null)
    {
        $data = $this->cajaService->getPagosCreditoSesion($id);
        
        if (!$data['cajaSesion']) {
            return redirect()->route('caja.index')
                ->with('warning', 'No hay sesión de caja disponible.');
        }

        // Verificar permisos de acceso
        $cajaSesion = $data['cajaSesion'];
        if (!$this->cajaService->esAdministrador() && $cajaSesion->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver esta sesión de caja.');
        }

        return view('caja.pagos_credito', [
            'cajaSesion' => $cajaSesion,
            'pagos' => $data['pagos'],
            'ventasCredito' => $data['ventasCredito'],
            'totales' => $data['totales'],
        ]);
    }
}
