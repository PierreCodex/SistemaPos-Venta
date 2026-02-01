<?php

namespace App\Http\Controllers;

use App\Services\CompraService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Controlador para el módulo de Compras
 */
class CompraController extends Controller
{
    protected CompraService $service;

    public function __construct(CompraService $service)
    {
        $this->service = $service;
    }

    /**
     * Muestra el listado de compras
     */
    public function index(): View
    {
        $compras = $this->service->obtenerTodas();
        $proveedores = $this->service->obtenerProveedoresParaCombo();
        $estadisticas = $this->service->obtenerEstadisticas();

        return view('compras.index', compact('compras', 'proveedores', 'estadisticas'));
    }

    /**
     * Muestra el formulario de nueva compra
     */
    public function create(): View
    {
        $proveedores = $this->service->obtenerProveedoresParaCombo();
        
        return view('compras.create', compact('proveedores'));
    }

    /**
     * Almacena una nueva compra
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'tipo_comprobante' => 'required|in:FACTURA,BOLETA,RECIBO,NOTA_ENTRADA,GUIA,TICKET',
            'numero_comprobante' => 'required|string|max:50',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_emision',
            'forma_pago' => 'required|in:CONTADO,CREDITO',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|numeric|min:0.001',
            'productos.*.costo_unitario' => 'required|numeric|min:0',
            'productos.*.subtotal' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'igv' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0'
        ]);

        try {
            $compra = $this->service->crear($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Compra registrada correctamente. Stock actualizado.',
                'data' => $compra
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la compra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra el detalle de una compra
     */
    public function show(string $id): JsonResponse
    {
        try {
            $compra = $this->service->buscarPorId((int) $id);

            return response()->json([
                'success' => true,
                'compra' => $compra
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Compra no encontrada'
            ], 404);
        }
    }

    /**
     * Anula una compra
     */
    public function anular(string $id): JsonResponse
    {
        try {
            $compra = $this->service->anular((int) $id);

            return response()->json([
                'success' => true,
                'message' => 'Compra anulada. Stock revertido.',
                'data' => $compra
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Busca productos para agregar a la compra
     */
    public function buscarProductos(Request $request): JsonResponse
    {
        $termino = $request->get('q', '');
        
        if (strlen($termino) < 1) {
            return response()->json([]);
        }

        $productos = $this->service->buscarProductos($termino);

        return response()->json($productos);
    }
}
