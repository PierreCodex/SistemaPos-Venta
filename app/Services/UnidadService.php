<?php

namespace App\Services;

use App\Models\Unidad;
use Illuminate\Database\Eloquent\Collection;

/**
 * Servicio para la lógica de negocio de Unidades de Medida.
 */
class UnidadService
{
    /**
     * Obtiene todas las unidades
     */
    public function obtenerTodas(): Collection
    {
        return Unidad::orderBy('nombre')->get();
    }

    /**
     * Obtiene unidades activas
     */
    public function obtenerActivas(): Collection
    {
        return Unidad::where('estado', true)
                     ->orderBy('nombre')
                     ->get();
    }

    /**
     * Busca una unidad por su ID
     */
    public function buscarPorId(int $id): Unidad
    {
        return Unidad::findOrFail($id);
    }

    /**
     * Crea una nueva unidad
     */
    public function crear(array $datos): Unidad
    {
        return Unidad::create([
            'codigo' => strtoupper($datos['codigo']),
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'estado' => $datos['estado'] ?? true,
        ]);
    }

    /**
     * Actualiza una unidad existente
     */
    public function actualizar(int $id, array $datos): Unidad
    {
        $unidad = $this->buscarPorId($id);
        
        $unidad->update([
            'codigo' => strtoupper($datos['codigo']),
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? $unidad->descripcion,
        ]);

        return $unidad->fresh();
    }

    /**
     * Elimina una unidad
     */
    public function eliminar(int $id): bool
    {
        $unidad = $this->buscarPorId($id);

        if ($unidad->productos()->withTrashed()->exists()) {
            throw new \Exception("No se puede eliminar la unidad porque tiene productos asociados. Intente desactivarla en su lugar.");
        }

        return $unidad->delete();
    }

    /**
     * Cambia el estado de una unidad (toggle)
     */
    public function toggleEstado(int $id): Unidad
    {
        $unidad = $this->buscarPorId($id);
        $unidad->estado = !$unidad->estado;
        $unidad->save();
        
        return $unidad;
    }
}
