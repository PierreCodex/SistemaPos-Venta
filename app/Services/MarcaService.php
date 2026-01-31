<?php

namespace App\Services;

use App\Models\Marca;
use Illuminate\Database\Eloquent\Collection;

/**
 * Servicio para la lógica de negocio de Marcas.
 * 
 * @package App\Services
 */
class MarcaService
{
    /**
     * Obtiene todas las marcas
     *
     * @return Collection
     */
    public function obtenerTodas(): Collection
    {
        return Marca::orderBy('nombre')
                        ->get();
    }

    /**
     * Obtiene categorías activas.
     *
     * @return Collection
     */
    public function obtenerActivas(): Collection
    {
        return Marca::where('estado', true)
                        ->orderBy('nombre')
                        ->get();
    }


    /**
     * Busca una marca por su ID.
     *
     * @param int $id
     * @return Marca
     */
    public function buscarPorId(int $id): Marca
    {
        return Marca::findOrFail($id);
    }

    /**
     * Crea una nueva marca.
     *
     * @param array $datos
     * @return Marca
     */
    public function crear(array $datos): Marca
    {
        return Marca::create([
            'codigo' => $datos['codigo'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'estado' => $datos['estado'] ?? true,
        ]);
    }

    /**
     * Actualiza una marca existente.
     *
     * @param int $id
     * @param array $datos
     * @return Marca
     */
    public function actualizar(int $id, array $datos): Marca
    {
        $marca = $this->buscarPorId($id);
        
        $marca->update([
            'codigo' => $datos['codigo'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? $marca->descripcion,
            'estado' => isset($datos['estado']) ? $datos['estado'] : $marca->estado,
        ]);

        return $marca->fresh(); // Refrescar para obtener datos actualizados
    }

    /**
     * Elimina una marca.
     *
     * @param int $id
     * @return bool
     */
    public function eliminar(int $id): bool
    {
        $marca = $this->buscarPorId($id);

        if ($marca->productos()->withTrashed()->exists()) {
            throw new \Exception("No se puede eliminar la marca porque tiene productos asociados. Intente desactivarla en su lugar.");
        }

        return $marca->delete();
    }

    /**
     * Obtiene las marcas activas para el combo.
     *
     * @return Collection
     */
    public function obtenerMarcasParaCombo(): Collection
    {
        return Marca::where('estado', true)
                              ->orderBy('nombre')
                              ->get();
    }

    /**
     * Cambia el estado de una marca (toggle).
     *
     * @param int $id
     * @return Marca
     */
    public function toggleEstado(int $id): Marca
    {
        $marca = $this->buscarPorId($id);
        $marca->estado = !$marca->estado;
        $marca->save();
        
        return $marca;
    }
}
