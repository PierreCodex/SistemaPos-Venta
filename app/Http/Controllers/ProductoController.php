<?php

namespace App\Http\Controllers;

use App\Services\ProductoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Requests\Producto\StoreProductoRequest;
use App\Http\Requests\Producto\UpdateProductoRequest;
use App\Models\Producto;
class ProductoController extends Controller
{
    protected ProductoService $service;

    public function __construct(ProductoService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $productos = $this->service->obtenerTodos();
        
        // Cargamos los datos para los selects del modal de registro
        $categorias = $this->service->obtenerCategoriasParaCombo();
        $marcas = $this->service->obtenerMarcasParaCombo();
        $unidades = $this->service->obtenerUnidadesParaCombo();
        $proveedores = $this->service->obtenerProveedoresParaCombo();

        return view('productos.index', compact(
            'productos', 
            'categorias', 
            'marcas', 
            'unidades', 
            'proveedores'
        ));
    }

    /**
     * Almacena un nuevo producto, procesa imagen e inicia Kardex (via Service).
     */
    public function store(StoreProductoRequest $request): JsonResponse|RedirectResponse
    {
        // El Service se encargará de: Guardar DB, subir imagen y crear registro en Kardex
        $producto = $this->service->crear($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto registrado correctamente e inventario inicializado.',
                'data' => $producto->load(['categoria', 'marca', 'unidad'])
            ]);
        }

        return redirect()->route('productos.index')
            ->with('success', 'Producto registrado correctamente.');
    }

    /**
     * Retorna los datos de un producto específico (útil para el modal de Ver/Editar).
     */
    public function show(string $id): JsonResponse
    {
        $producto = $this->service->buscarPorId((int) $id);
        
        return response()->json([
            'success' => true,
            'producto' => $producto->load(['categoria', 'marca', 'unidad', 'proveedor'])
        ]);
    }

    /**
     * Actualiza el producto (Service maneja si hay cambio de imagen).
     */
    public function update(UpdateProductoRequest $request, string $id): JsonResponse|RedirectResponse
    {
        $producto = $this->service->actualizar((int) $id, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado correctamente.',
                'data' => $producto->load(['categoria', 'marca', 'unidad'])
            ]);
        }

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Eliminación lógica (Soft Delete).
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $this->service->eliminar((int) $id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado del catálogo.',
                'id' => (int) $id
            ]);
        }

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado.');
    }

    /**
     * Cambia el estado Activo/Inactivo.
     */
    public function toggleEstado(string $id): JsonResponse
    {
        $producto = $this->service->toggleEstado((int) $id);

        return response()->json([
            'success' => true,
            'estado' => $producto->estado,
            'message' => $producto->estado 
                ? 'Producto habilitado para la venta.' 
                : 'Producto deshabilitado para la venta.'
        ]);
    }
}

