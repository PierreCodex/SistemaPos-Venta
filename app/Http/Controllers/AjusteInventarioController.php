<?php

namespace App\Http\Controllers;

use App\Services\AjusteInventarioService;
use App\Models\AjusteInventario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Controlador para el módulo de Ajustes de Inventario
 */
class AjusteInventarioController extends Controller
{
    protected AjusteInventarioService $service;

    public function __construct(AjusteInventarioService $service)
    {
        $this->service = $service;
    }

    /**
     * Muestra el listado de ajustes
     */
    public function index(): View
    {
        $ajustes = $this->service->obtenerTodos();
        $tipos = AjusteInventario::getTipos();
        $motivos = AjusteInventario::getMotivos();
        $productosStockBajo = $this->service->productosStockBajo();

        return view('inventario.ajustes.index', compact('ajustes', 'tipos', 'motivos', 'productosStockBajo'));
    }

    /**
     * Muestra el formulario de nuevo ajuste
     */
    public function create(): View
    {
        $tipos = AjusteInventario::getTipos();
        $motivos = AjusteInventario::getMotivos();

        return view('inventario.ajustes.create', compact('tipos', 'motivos'));
    }

    /**
     * Almacena un nuevo ajuste
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'tipo' => 'required|in:ENTRADA,SALIDA,CONTEO',
            'motivo' => 'required|in:MERMA,ROBO,VENCIMIENTO,ERROR_CONTEO,DONACION,OTRO',
            'descripcion' => 'nullable|string|max:500',
            'fecha' => 'nullable|date',
            'aplicar_ahora' => 'nullable|boolean',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.stock_fisico' => 'required|numeric|min:0',
            'productos.*.observacion' => 'nullable|string|max:255'
        ]);

        try {
            $ajuste = $this->service->crear($request->all());
            $mensaje = $request->input('aplicar_ahora') 
                ? 'Ajuste aplicado correctamente. Stock actualizado.' 
                : 'Ajuste guardado como borrador.';

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'data' => $ajuste
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el ajuste: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra el detalle de un ajuste
     */
    public function show(string $id): JsonResponse
    {
        try {
            $ajuste = $this->service->buscarPorId((int) $id);

            return response()->json([
                'success' => true,
                'ajuste' => $ajuste
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ajuste no encontrado'
            ], 404);
        }
    }

    /**
     * Aplica un ajuste en borrador
     */
    public function aplicar(string $id): JsonResponse
    {
        try {
            $ajuste = $this->service->aplicar((int) $id);

            return response()->json([
                'success' => true,
                'message' => 'Ajuste aplicado correctamente. Stock actualizado.',
                'data' => $ajuste
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Anula un ajuste aplicado
     */
    public function anular(string $id): JsonResponse
    {
        try {
            $ajuste = $this->service->anular((int) $id);

            return response()->json([
                'success' => true,
                'message' => 'Ajuste anulado. Stock revertido.',
                'data' => $ajuste
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Elimina un ajuste en borrador
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->service->eliminar((int) $id);

            return response()->json([
                'success' => true,
                'message' => 'Ajuste eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Busca productos para el ajuste
     */
    public function buscarProductos(Request $request): JsonResponse
    {
        $termino = $request->get('q', '');
        
        if (strlen($termino) < 2) {
            return response()->json([]);
        }

        $productos = $this->service->buscarProductos($termino);

        return response()->json($productos);
    }
}
