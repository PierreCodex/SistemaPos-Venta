<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriaGlobal\StoreCategoriaGlobalRequest;
use App\Http\Requests\CategoriaGlobal\UpdateCategoriaGlobalRequest;
use App\Services\CategoriaGlobalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador para gestionar Categorías Globales.
 * 
 * Optimizado para AJAX: retorna JSON cuando la petición es AJAX.
 * 
 * @package App\Http\Controllers
 */
class CategoriaGlobalController extends Controller
{
    protected CategoriaGlobalService $service;

    public function __construct(CategoriaGlobalService $service)
    {
        $this->service = $service;
    }

    /**
     * Muestra el listado de categorías globales.
     */
    public function index(): View
    {
        $categoriasGlobales = $this->service->obtenerTodas();
        return view('categorias-globales.index', compact('categoriasGlobales'));
    }

    /**
     * Almacena una nueva categoría global (AJAX optimizado).
     */
    public function store(StoreCategoriaGlobalRequest $request): JsonResponse|RedirectResponse
    {
        $categoria = $this->service->crear($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Categoría Global creada correctamente.',
                'categoria' => [
                    'id' => $categoria->id,
                    'nombre' => $categoria->nombre,
                    'descripcion' => $categoria->descripcion,
                    'estado' => $categoria->estado,
                ]
            ]);
        }

        return redirect()
            ->route('categorias-globales.index')
            ->with('success', 'Categoría Global creada correctamente.');
    }

    /**
     * Actualiza una categoría global (AJAX optimizado).
     */
    public function update(UpdateCategoriaGlobalRequest $request, string $id): JsonResponse|RedirectResponse
    {
        $categoria = $this->service->actualizar((int) $id, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Categoría Global actualizada correctamente.',
                'categoria' => [
                    'id' => $categoria->id,
                    'nombre' => $categoria->nombre,
                    'descripcion' => $categoria->descripcion,
                    'estado' => $categoria->estado,
                ]
            ]);
        }

        return redirect()
            ->route('categorias-globales.index')
            ->with('success', 'Categoría Global actualizada correctamente.');
    }

    /**
     * Elimina una categoría global (AJAX optimizado).
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        try {
            $this->service->eliminar((int) $id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoría Global eliminada correctamente.',
                    'id' => (int) $id
                ]);
            }

            return redirect()
                ->route('categorias-globales.index')
                ->with('success', 'Categoría Global eliminada correctamente.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()
                ->route('categorias-globales.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Toggle de estado (AJAX).
     */
    public function toggleEstado(string $id): JsonResponse
    {
        $categoria = $this->service->toggleEstado((int) $id);

        return response()->json([
            'success' => true,
            'estado' => $categoria->estado,
            'message' => $categoria->estado 
                ? 'Categoría Global activada correctamente.' 
                : 'Categoría Global desactivada correctamente.'
        ]);
    }
}
