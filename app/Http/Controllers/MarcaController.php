<?php

namespace App\Http\Controllers;

use App\Services\MarcaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Requests\Marca\StoreMarcaRequest;
use App\Http\Requests\Marca\UpdateMarcaRequest;
use App\Models\Marca;


class MarcaController extends Controller
{

    protected MarcaService $service;

    public function __construct(MarcaService $service)
    {
        $this->service = $service;
    }

 
    /**
     * Muestra el listado de marcas.
     */
    public function index(): View
    {
        $marcas = $this->service->obtenerTodas();
        
        return view('marcas.index', compact('marcas'));
    }

     /**
     * Almacena una nueva marca (AJAX optimizado).
     */
    public function store(StoreMarcaRequest $request): JsonResponse|RedirectResponse
    {
        $marca = $this->service->crear($request->validated());
        

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Marca creada correctamente.',
                'marca' => [
                    'id' => $marca->id,
                    'codigo' => $marca->codigo,
                    'nombre' => $marca->nombre,
                    'descripcion' => $marca->descripcion,
                    'estado' => $marca->estado,
                ]
            ]);
        }

        return redirect()
            ->route('marcas.index')
            ->with('success', 'Marca creada correctamente.');
    }


    



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       $marca = $this->service->buscarPorId((int) $id);
        return view('marcas.show', compact('marca'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Marca $marca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
  public function update(UpdateMarcaRequest $request, string $id): JsonResponse|RedirectResponse
    {
        $marca = $this->service->actualizar((int) $id, $request->validated());
      

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Marca actualizada correctamente.',
                'marca' => [
                    'id' => $marca->id,
                    'codigo' => $marca->codigo,
                    'nombre' => $marca->nombre,
                    'descripcion' => $marca->descripcion,
                    'estado' => $marca->estado,
                ]
            ]);
        }

        return redirect()
            ->route('marcas.index')
            ->with('success', 'Marca actualizada correctamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        try {
            $this->service->eliminar((int) $id);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Marca eliminada correctamente.',
                    'id' => (int) $id
                ]);
            }

            return redirect()
                ->route('marcas.index')
                ->with('success', 'Marca eliminada correctamente.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()
                ->route('marcas.index')
                ->with('error', $e->getMessage());
        }
    }

    public function toggleEstado(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $marca = $this->service->toggleEstado((int) $id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Estado de la marca actualizado correctamente.',
                'marca' => [
                    'id' => $marca->id,
                    'estado' => $marca->estado,
                ]
            ]);
        }

        return redirect()
            ->route('marcas.index')
            ->with('success', 'Estado de la marca actualizado correctamente.');
    }

}
