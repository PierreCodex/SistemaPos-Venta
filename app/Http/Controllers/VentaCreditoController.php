<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaCreditoPago;
use App\Services\CajaService;
use App\Models\CajaMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VentaCreditoController extends Controller
{
    protected CajaService $cajaService;

    public function __construct(CajaService $cajaService)
    {
        $this->cajaService = $cajaService;
    }

    /**
     * Verifica si el usuario actual es administrador
     */
    protected function esAdmin(): bool
    {
        return Auth::user()->hasAnyRole(['super-admin', 'Admin', 'administrador']);
    }

    /**
     * Aplica filtro de usuario a una query de Venta si no es admin
     */
    protected function aplicarFiltroUsuario($query)
    {
        if (!$this->esAdmin()) {
            $query->where('user_id', Auth::id());
        }
        return $query;
    }

    /**
     * Listado de ventas a crédito
     */
    public function index(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));

        $query = Venta::with(['cliente', 'vendedor'])
            ->where('es_credito', true)
            ->where('saldo_pendiente', '>', 0)
            ->whereBetween('fecha_emision', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ])
            ->orderBy('fecha_emision', 'desc');

        // Vendedores solo ven sus propios créditos
        $this->aplicarFiltroUsuario($query);

        $ventas = $query->get();

        // Estadísticas
        $estadisticas = [
            'total' => $ventas->sum('total'),
            'saldo_pendiente' => $ventas->sum('saldo_pendiente')
        ];

        return view('ventas_credito.index', compact('ventas', 'estadisticas', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Registra un nuevo pago (abono) para una venta a crédito
     */
    public function registrarPago(Request $request, $id)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|string',
            'fecha_pago' => 'required|date',
            'numero_operacion' => 'nullable|string',
            'observaciones' => 'nullable|string'
        ]);

        try {
            return DB::transaction(function () use ($request, $id) {
                $venta = Venta::findOrFail($id);

                // Vendedor solo puede cobrar sus propias ventas
                if (!$this->esAdmin() && (int) $venta->user_id !== (int) Auth::id()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permiso para cobrar esta venta.'
                    ], 403);
                }

                if ($request->monto > $venta->saldo_pendiente) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El monto del pago no puede ser mayor al saldo pendiente (S/ ' . number_format($venta->saldo_pendiente, 2) . ')'
                    ], 422);
                }

                $numero_operacion = $request->numero_operacion;
                if (empty($numero_operacion)) {
                    $numero_operacion = 'REC-' . strtoupper(Str::random(8));
                }

                // Crear el registro de pago
                $cajaSesionId = $this->cajaService->getCajaAbiertaId();
                
                VentaCreditoPago::create([
                    'venta_id' => $venta->id,
                    'monto' => $request->monto,
                    'metodo_pago' => $request->metodo_pago,
                    'fecha_pago' => $request->fecha_pago,
                    'numero_operacion' => $numero_operacion,
                    'user_id' => Auth::id(),
                    'caja_sesion_id' => $cajaSesionId,
                    'observaciones' => $request->observaciones
                ]);

                // Registrar movimiento en caja si es pago en efectivo y hay caja abierta
                $mensajeCaja = '';
                if (strtoupper($request->metodo_pago) === 'EFECTIVO') {
                    if ($this->cajaService->existeCajaAbierta()) {
                        $this->cajaService->registrarMovimiento(
                            CajaMovimiento::TIPO_INGRESO,
                            CajaMovimiento::CONCEPTO_PAGO_CLIENTE,
                            $request->monto,
                            "Pago crédito Venta #{$venta->id} - {$venta->comprobante_completo}",
                            'ventas',
                            $venta->id
                        );
                        $mensajeCaja = ' (Registrado en caja)';
                    }
                }

                // Actualizar saldo de la venta
                $nuevo_saldo = $venta->saldo_pendiente - $request->monto;
                $venta->saldo_pendiente = $nuevo_saldo;
                
                if ($nuevo_saldo <= 0) {
                    $venta->estado_pago = 'PAGADO';
                } else {
                    $venta->estado_pago = 'PARCIAL';
                }
                
                $venta->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Pago registrado correctamente' . $mensajeCaja,
                    'nuevo_saldo' => number_format($nuevo_saldo, 2),
                    'estado_pago' => $venta->estado_pago,
                    'registrado_en_caja' => strtoupper($request->metodo_pago) === 'EFECTIVO' && $this->cajaService->existeCajaAbierta()
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene el historial de pagos de una venta
     */
    public function historialPagos($id)
    {
        // Verificar acceso si no es admin
        if (!$this->esAdmin()) {
            $venta = Venta::findOrFail($id);
            if ((int) $venta->user_id !== (int) Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Sin permiso.'], 403);
            }
        }

        $pagos = VentaCreditoPago::with('user')
            ->where('venta_id', $id)
            ->orderBy('fecha_pago', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'pagos' => $pagos
        ]);
    }

    /**
     * Ver detalle de la venta formateado para el modal
     */
    public function show($id)
    {
        $venta = Venta::with(['cliente', 'vendedor', 'detalles.producto.unidad', 'comprobanteElectronico'])->findOrFail($id);
        
        // Verificar acceso si no es admin
        if (!$this->esAdmin() && (int) $venta->user_id !== (int) Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Sin permiso.'], 403);
        }

        $data = [
            'cabecera' => [
                'codigo_venta' => $venta->comprobante,
                'fecha_emision' => $venta->fecha_emision->format('d/m/Y, H:i:s'),
                'comprobante_tipo' => $venta->comprobante,
                'comprobante_num' => $venta->serie . '-' . str_pad($venta->numero, 8, '0', STR_PAD_LEFT)
            ],
            'cliente' => [
                'nombre' => $venta->nombre_cliente,
                'documento' => $venta->cliente ? $venta->cliente->numero_documento : 'S/D',
                'es_generico' => $venta->cliente ? false : true
            ],
            'finanzas' => [
                'moneda' => 'PEN',
                'simbolo' => 'S/',
                'total_venta' => (float)$venta->total,
                'monto_credito' => (float)$venta->saldo_pendiente,
                'metodo_pago' => $venta->metodo_pago
            ],
            'productos' => $venta->detalles->map(function($detalle) {
                return [
                    'nombre' => $detalle->producto->nombre,
                    'cantidad' => (float)$detalle->cantidad,
                    'unidad' => $detalle->producto->unidad->codigo ?? 'UND',
                    'formato_decimal' => $detalle->producto->unidad->permite_decimales ?? false,
                    'subtotal' => (float)$detalle->subtotal
                ];
            })
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Historial general de todos los pagos realizados a créditos
     */
    public function historialGeneral(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));

        $query = VentaCreditoPago::with(['venta.cliente', 'user'])
            ->whereBetween('fecha_pago', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ]);

        // Vendedores solo ven pagos que ellos cobraron
        if (!$this->esAdmin()) {
            $query->where('user_id', Auth::id());
        }

        $pagos = $query->orderBy('fecha_pago', 'desc')->get();

        $totalRecaudado = $pagos->sum('monto');

        return view('ventas_credito.historial', compact('pagos', 'totalRecaudado', 'fechaInicio', 'fechaFin'));
    }
}
