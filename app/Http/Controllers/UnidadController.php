<?php

namespace App\Http\Controllers;

use App\Services\UnidadService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Requests\Unidad\StoreUnidadRequest;
use App\Http\Requests\Unidad\UpdateUnidadRequest;

class UnidadController extends Controller
{
    protected UnidadService $service;

    public function __construct(UnidadService $service)
    {
        $this->service = $service;
    }

    /**
     * Muestra el listado de unidades.
     */
    public function index(): View
    {
        $unidades = $this->service->obtenerTodas();
        return view('unidades.index', compact('unidades'));
    }

    /**
     * Almacena una nueva unidad (AJAX optimizado).
     */
    public function store(StoreUnidadRequest $request): JsonResponse|RedirectResponse
    {
        $unidad = $this->service->crear($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Unidad creada correctamente.',
                'unidad' => [
                    'id' => $unidad->id,
                    'codigo' => $unidad->codigo,
                    'nombre' => $unidad->nombre,
                    'descripcion' => $unidad->descripcion,
                    'estado' => $unidad->estado,
                ]
            ]);
        }

        return redirect()
            ->route('unidades.index')
            ->with('success', 'Unidad creada correctamente.');
    }

    /**
     * Muestra una unidad específica.
     */
    public function show(string $id): View
    {
        $unidad = $this->service->buscarPorId((int) $id);
        return view('unidades.show', compact('unidad'));
    }

    /**
     * Actualiza una unidad (AJAX optimizado).
     */
    public function update(UpdateUnidadRequest $request, string $id): JsonResponse|RedirectResponse
    {
        $unidad = $this->service->actualizar((int) $id, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Unidad actualizada correctamente.',
                'unidad' => [
                    'id' => $unidad->id,
                    'codigo' => $unidad->codigo,
                    'nombre' => $unidad->nombre,
                    'descripcion' => $unidad->descripcion,
                    'estado' => $unidad->estado,
                ]
            ]);
        }

        return redirect()
            ->route('unidades.index')
            ->with('success', 'Unidad actualizada correctamente.');
    }

    /**
     * Elimina una unidad (AJAX optimizado).
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        try {
            $this->service->eliminar((int) $id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Unidad eliminada correctamente.',
                    'id' => (int) $id
                ]);
            }

            return redirect()
                ->route('unidades.index')
                ->with('success', 'Unidad eliminada correctamente.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()
                ->route('unidades.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Toggle de estado (AJAX).
     */
    public function toggleEstado(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $unidad = $this->service->toggleEstado((int) $id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Estado de la unidad actualizado correctamente.',
                'unidad' => [
                    'id' => $unidad->id,
                    'estado' => $unidad->estado,
                ]
            ]);
        }

        return redirect()
            ->route('unidades.index')
            ->with('success', 'Estado de la unidad actualizado correctamente.');
    }
}
