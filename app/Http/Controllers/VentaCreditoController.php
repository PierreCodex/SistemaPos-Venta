<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaCreditoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VentaCreditoController extends Controller
{
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
                VentaCreditoPago::create([
                    'venta_id' => $venta->id,
                    'monto' => $request->monto,
                    'metodo_pago' => $request->metodo_pago,
                    'fecha_pago' => $request->fecha_pago,
                    'numero_operacion' => $numero_operacion,
                    'user_id' => Auth::id(),
                    'observaciones' => $request->observaciones
                ]);

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
                    'message' => 'Pago registrado correctamente',
                    'nuevo_saldo' => number_format($nuevo_saldo, 2),
                    'estado_pago' => $venta->estado_pago
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

        $pagos = VentaCreditoPago::with(['venta.cliente', 'user'])
            ->whereBetween('fecha_pago', [
                Carbon::parse($fechaInicio)->startOfDay(),
                Carbon::parse($fechaFin)->endOfDay()
            ])
            ->orderBy('fecha_pago', 'desc')
            ->get();

        $totalRecaudado = $pagos->sum('monto');

        return view('ventas_credito.historial', compact('pagos', 'totalRecaudado', 'fechaInicio', 'fechaFin'));
    }
}
