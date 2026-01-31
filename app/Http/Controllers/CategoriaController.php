<?php

namespace App\Http\Controllers;

use App\Http\Requests\Categoria\StoreCategoriaRequest;
use App\Http\Requests\Categoria\UpdateCategoriaRequest;
use App\Services\CategoriaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador para gestionar Categorías (Subcategorías).
 * 
 * Optimizado para AJAX: retorna JSON cuando la petición es AJAX.
 * 
 * @package App\Http\Controllers
 */
class CategoriaController extends Controller
{
    protected CategoriaService $service;

    public function __construct(CategoriaService $service)
    {
        $this->service = $service;
    }

    /**
     * Muestra el listado de categorías.
     */
    public function index(): View
    {
        $categorias = $this->service->obtenerTodas();
        $categoriasGlobales = $this->service->obtenerCategoriasGlobalesParaCombo();
        
        return view('categorias.index', compact('categorias', 'categoriasGlobales'));
    }

    /**
     * Almacena una nueva categoría (AJAX optimizado).
     */
    public function store(StoreCategoriaRequest $request): JsonResponse|RedirectResponse
    {
        $categoria = $this->service->crear($request->validated());
        
        // Cargar la relación para obtener el nombre de la categoría global
        $categoria->load('categoriaGlobal');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Categoría creada correctamente.',
                'categoria' => [
                    'id' => $categoria->id,
                    'nombre' => $categoria->nombre,
                    'descripcion' => $categoria->descripcion,
                    'estado' => $categoria->estado,
                    'categoria_global_id' => $categoria->categoria_global_id,
                    'categoria_global_nombre' => $categoria->categoriaGlobal->nombre ?? '-',
                ]
            ]);
        }

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Muestra los detalles de una categoría.
     */
    public function show(string $id): View
    {
        $categoria = $this->service->buscarPorId((int) $id);
        return view('categorias.show', compact('categoria'));
    }

    /**
     * Actualiza una categoría (AJAX optimizado).
     */
    public function update(UpdateCategoriaRequest $request, string $id): JsonResponse|RedirectResponse
    {
        $categoria = $this->service->actualizar((int) $id, $request->validated());
        $categoria->load('categoriaGlobal');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada correctamente.',
                'categoria' => [
                    'id' => $categoria->id,
                    'nombre' => $categoria->nombre,
                    'descripcion' => $categoria->descripcion,
                    'estado' => $categoria->estado,
                    'categoria_global_id' => $categoria->categoria_global_id,
                    'categoria_global_nombre' => $categoria->categoriaGlobal->nombre ?? '-',
                ]
            ]);
        }

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Elimina una categoría (AJAX optimizado).
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        try {
            $this->service->eliminar((int) $id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoría eliminada correctamente.',
                    'id' => (int) $id
                ]);
            }

            return redirect()
                ->route('categorias.index')
                ->with('success', 'Categoría eliminada correctamente.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()
                ->route('categorias.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Toggle de estado (ya era AJAX).
     */
    public function toggleEstado(string $id): JsonResponse
    {
        $categoria = $this->service->toggleEstado((int) $id);

        return response()->json([
            'success' => true,
            'estado' => $categoria->estado,
            'message' => $categoria->estado 
                ? 'Categoría activada correctamente.' 
                : 'Categoría desactivada correctamente.'
        ]);
    }
}
