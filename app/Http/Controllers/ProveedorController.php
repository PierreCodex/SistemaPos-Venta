<?php

namespace App\Http\Controllers;

use App\Services\ProveedorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Requests\Proveedor\StoreProveedorRequest;
use App\Http\Requests\Proveedor\UpdateProveedorRequest;

class ProveedorController extends Controller
{
    protected ProveedorService $service;

    public function __construct(ProveedorService $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        $proveedores = $this->service->obtenerTodos();
        return view('proveedores.index', compact('proveedores'));
    }

    public function store(StoreProveedorRequest $request): JsonResponse|RedirectResponse
    {
        $proveedor = $this->service->crear($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Proveedor creado correctamente.',
                'proveedor' => [
                    'id' => $proveedor->id,
                    'tipo_documento' => $proveedor->tipo_documento,
                    'documento' => $proveedor->documento,
                    'nombre' => $proveedor->nombre,
                    'telefono' => $proveedor->telefono,
                    'email' => $proveedor->email,
                    'direccion' => $proveedor->direccion,
                    'estado' => $proveedor->estado,
                ]
            ]);
        }

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado correctamente.');
    }

    public function show(string $id)
    {
        $proveedor = $this->service->buscarPorId((int) $id);
        return view('proveedores.show', compact('proveedor'));
    }

    public function update(UpdateProveedorRequest $request, string $id): JsonResponse|RedirectResponse
    {
        $proveedor = $this->service->actualizar((int) $id, $request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Proveedor actualizado correctamente.',
                'proveedor' => [
                    'id' => $proveedor->id,
                    'tipo_documento' => $proveedor->tipo_documento,
                    'documento' => $proveedor->documento,
                    'nombre' => $proveedor->nombre,
                    'telefono' => $proveedor->telefono,
                    'email' => $proveedor->email,
                    'direccion' => $proveedor->direccion,
                    'estado' => $proveedor->estado,
                ]
            ]);
        }

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $this->service->eliminar((int) $id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Proveedor eliminado correctamente.',
                'id' => (int) $id
            ]);
        }

        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado correctamente.');
    }

    public function toggleEstado(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $proveedor = $this->service->toggleEstado((int) $id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $proveedor->estado ? 'Proveedor activado.' : 'Proveedor desactivado.',
                'estado' => $proveedor->estado
            ]);
        }

        return redirect()->route('proveedores.index')->with('success', 'Estado actualizado.');
    }
}
