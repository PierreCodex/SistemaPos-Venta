<?php

namespace App\Services;

use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Collection;

/**
 * Servicio para la lógica de negocio de Proveedores.
 */
class ProveedorService
{
    public function obtenerTodos(): Collection
    {
        return Proveedor::orderBy('nombre')->get();
    }

    public function obtenerActivos(): Collection
    {
        return Proveedor::activos()->orderBy('nombre')->get();
    }

    public function buscarPorId(int $id): Proveedor
    {
        return Proveedor::findOrFail($id);
    }

    public function crear(array $datos): Proveedor
    {
        return Proveedor::create([
            'tipo_documento' => $datos['tipo_documento'],
            'documento' => $datos['documento'],
            'nombre' => $datos['nombre'],
            'telefono' => $datos['telefono'] ?? null,
            'email' => $datos['email'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
            'estado' => $datos['estado'] ?? true,
        ]);
    }

    public function actualizar(int $id, array $datos): Proveedor
    {
        $proveedor = $this->buscarPorId($id);
        
        $proveedor->update([
            'tipo_documento' => $datos['tipo_documento'],
            'documento' => $datos['documento'],
            'nombre' => $datos['nombre'],
            'telefono' => $datos['telefono'] ?? $proveedor->telefono,
            'email' => $datos['email'] ?? $proveedor->email,
            'direccion' => $datos['direccion'] ?? $proveedor->direccion,
        ]);

        return $proveedor->fresh();
    }

    public function eliminar(int $id): bool
    {
        $proveedor = $this->buscarPorId($id);
        return $proveedor->delete();
    }

    public function toggleEstado(int $id): Proveedor
    {
        $proveedor = $this->buscarPorId($id);
        $proveedor->estado = !$proveedor->estado;
        $proveedor->save();
        
        return $proveedor;
    }
}
