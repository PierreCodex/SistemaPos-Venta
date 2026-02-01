<?php

namespace App\Http\Controllers;

use App\Services\KardexService;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Controlador para el módulo de Kardex (Historial de Movimientos)
 */
class KardexController extends Controller
{
    protected KardexService $service;

    public function __construct(KardexService $service)
    {
        $this->service = $service;
    }

    /**
     * Muestra el historial de movimientos
     */
    public function index(Request $request): View
    {
        $filtros = $request->only(['producto_id', 'tipo_movimiento', 'fecha_desde', 'fecha_hasta', 'per_page']);
        $movimientos = $this->service->obtenerTodos($filtros);
        $tiposMovimiento = KardexService::getTiposMovimiento();
        $estadisticas = $this->service->obtenerEstadisticas();
        $ultimosMovimientos = $this->service->ultimosMovimientos(5);

        return view('inventario.kardex.index', compact(
            'movimientos', 
            'tiposMovimiento', 
            'estadisticas', 
            'ultimosMovimientos',
            'filtros'
        ));
    }

    /**
     * Muestra el kardex de un producto específico
     */
    public function porProducto(Request $request, string $productoId): View
    {
        $producto = Producto::with('unidad')->findOrFail($productoId);
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        
        $movimientos = $this->service->obtenerPorProducto((int) $productoId, $fechaDesde, $fechaHasta);
        $tiposMovimiento = KardexService::getTiposMovimiento();

        return view('inventario.kardex.producto', compact('producto', 'movimientos', 'tiposMovimiento', 'fechaDesde', 'fechaHasta'));
    }

    /**
     * API: Obtiene movimientos en formato JSON
     */
    public function obtenerMovimientos(Request $request): JsonResponse
    {
        $filtros = $request->only(['producto_id', 'tipo_movimiento', 'fecha_desde', 'fecha_hasta', 'per_page']);
        $movimientos = $this->service->obtenerTodos($filtros);

        return response()->json([
            'success' => true,
            'data' => $movimientos
        ]);
    }

    /**
     * API: Obtiene estadísticas del inventario
     */
    public function estadisticas(): JsonResponse
    {
        $estadisticas = $this->service->obtenerEstadisticas();

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }

    /**
     * API: Obtiene resumen por tipo de movimiento
     */
    public function resumenPorTipo(Request $request): JsonResponse
    {
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');

        $resumen = $this->service->resumenPorTipo($fechaDesde, $fechaHasta);

        return response()->json([
            'success' => true,
            'data' => $resumen
        ]);
    }

    /**
     * Exporta el kardex de un producto
     */
    public function exportar(Request $request, string $productoId): JsonResponse
    {
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');

        $datos = $this->service->exportar((int) $productoId, $fechaDesde, $fechaHasta);

        return response()->json([
            'success' => true,
            'data' => $datos
        ]);
    }

    /**
     * Busca productos para ver su kardex
     */
    public function buscarProductos(Request $request): JsonResponse
    {
        $termino = $request->get('q', '');
        
        if (strlen($termino) < 2) {
            return response()->json([]);
        }

        $productos = Producto::where('estado', true)
            ->where(function ($q) use ($termino) {
                $q->where('nombre', 'like', "%{$termino}%")
                  ->orWhere('codigo', 'like', "%{$termino}%")
                  ->orWhere('codigo_barras', 'like', "%{$termino}%");
            })
            ->with('unidad')
            ->limit(20)
            ->get();

        return response()->json($productos);
    }
}
