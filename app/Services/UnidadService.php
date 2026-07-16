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
            'permite_decimales' => $datos['permite_decimales'] ?? false,
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
            'permite_decimales' => $datos['permite_decimales'] ?? $unidad->permite_decimales,
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

        // Una unidad puede no estar en ningún producto pero sí ser la unidad de
        // una presentación (ej: "Caja" en un producto cuya base es "Unidad").
        // Sin esta guarda, la FK restrictOnDelete devolvería un error SQL crudo.
        if ($unidad->presentaciones()->exists()) {
            throw new \Exception("No se puede eliminar la unidad porque se usa en presentaciones de productos. Intente desactivarla en su lugar.");
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
