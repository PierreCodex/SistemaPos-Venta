<?php

namespace App\Http\Controllers;

use App\Http\Requests\Presentacion\StorePresentacionRequest;
use App\Http\Requests\Presentacion\UpdatePresentacionRequest;
use App\Models\Producto;
use App\Models\ProductoPresentacion;
use App\Models\Unidad;
use App\Services\ProductoPresentacionService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Gestión de presentaciones de un producto.
 *
 * Todas las rutas cuelgan de un producto (productos/{producto}/presentaciones)
 * y se protegen con el permiso productos.editar: una presentación es un
 * atributo del producto.
 */
class ProductoPresentacionController extends Controller
{
    public function __construct(private ProductoPresentacionService $service)
    {
    }

    /**
     * Pantalla de gestión de las presentaciones de un producto.
     */
    public function index(Producto $producto): View
    {
        $presentaciones = $this->service->listar($producto);
        $unidades = Unidad::where('estado', true)->orderBy('nombre')->get();

        // Datos ya serializados para el JS (incluye tiene_movimientos, que
        // decide si el factor se puede editar). Se preparan aquí porque
        // @json() no maneja bien una arrow-function con arrays anidados.
        $presentacionesJson = $presentaciones
            ->map(fn (ProductoPresentacion $p) => $this->serializar($p) + [
                'tiene_movimientos' => $p->tieneMovimientos(),
            ])
            ->values();

        return view('presentaciones.index', compact('producto', 'presentaciones', 'unidades', 'presentacionesJson'));
    }

    /**
     * Crea una presentación para el producto.
     */
    public function store(StorePresentacionRequest $request, Producto $producto): JsonResponse
    {
        try {
            $presentacion = $this->service->crear($producto, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Presentación creada correctamente.',
                'presentacion' => $this->serializar($presentacion),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Actualiza una presentación del producto.
     */
    public function update(UpdatePresentacionRequest $request, Producto $producto, ProductoPresentacion $presentacion): JsonResponse
    {
        $this->verificarPertenencia($producto, $presentacion);

        try {
            $presentacion = $this->service->actualizar($presentacion, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Presentación actualizada correctamente.',
                'presentacion' => $this->serializar($presentacion),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Activa o desactiva una presentación.
     */
    public function toggleEstado(Producto $producto, ProductoPresentacion $presentacion): JsonResponse
    {
        $this->verificarPertenencia($producto, $presentacion);

        try {
            $presentacion = $this->service->toggleEstado($presentacion);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado.',
                'estado' => $presentacion->estado,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Elimina una presentación.
     */
    public function destroy(Producto $producto, ProductoPresentacion $presentacion): JsonResponse
    {
        $this->verificarPertenencia($producto, $presentacion);

        try {
            $this->service->eliminar($presentacion);

            return response()->json([
                'success' => true,
                'message' => 'Presentación eliminada correctamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Impide operar sobre la presentación de otro producto vía URL manipulada.
     */
    private function verificarPertenencia(Producto $producto, ProductoPresentacion $presentacion): void
    {
        abort_if($presentacion->producto_id !== $producto->id, 404);
    }

    private function serializar(ProductoPresentacion $p): array
    {
        return [
            'id' => $p->id,
            'unidad_id' => $p->unidad_id,
            'unidad_codigo' => $p->unidad->codigo ?? '',
            'unidad_nombre' => $p->unidad->nombre ?? '',
            'factor' => (float) $p->factor,
            'precio_venta' => (float) $p->precio_venta,
            'codigo_barras' => $p->codigo_barras,
            'es_base' => (bool) $p->es_base,
            'estado' => (bool) $p->estado,
        ];
    }
}
