<?php

namespace App\Services;

use App\Models\Categoria;
use App\Models\CategoriaGlobal;
use Illuminate\Database\Eloquent\Collection;

/**
 * Servicio para la lógica de negocio de Categorías (Subcategorías).
 * 
 * @package App\Services
 */
class CategoriaService
{
    /**
     * Obtiene todas las categorías con su categoría global.
     *
     * @return Collection
     */
    public function obtenerTodas(): Collection
    {
        return Categoria::with('categoriaGlobal')
                        ->orderBy('nombre')
                        ->get();
    }

    /**
     * Obtiene categorías activas.
     *
     * @return Collection
     */
    public function obtenerActivas(): Collection
    {
        return Categoria::with('categoriaGlobal')
                        ->where('estado', true)
                        ->orderBy('nombre')
                        ->get();
    }

    /**
     * Obtiene categorías por categoría global.
     *
     * @param int $categoriaGlobalId
     * @return Collection
     */
    public function obtenerPorCategoriaGlobal(int $categoriaGlobalId): Collection
    {
        return Categoria::where('categoria_global_id', $categoriaGlobalId)
                        ->where('estado', true)
                        ->orderBy('nombre')
                        ->get();
    }

    /**
     * Busca una categoría por su ID.
     *
     * @param int $id
     * @return Categoria
     */
    public function buscarPorId(int $id): Categoria
    {
        return Categoria::with('categoriaGlobal')->findOrFail($id);
    }

    /**
     * Crea una nueva categoría.
     *
     * @param array $datos
     * @return Categoria
     */
    public function crear(array $datos): Categoria
    {
        return Categoria::create([
            'categoria_global_id' => $datos['categoria_global_id'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'estado' => $datos['estado'] ?? true,
        ]);
    }

    /**
     * Actualiza una categoría existente.
     *
     * @param int $id
     * @param array $datos
     * @return Categoria
     */
    public function actualizar(int $id, array $datos): Categoria
    {
        $categoria = $this->buscarPorId($id);
        
        $categoria->update([
            'categoria_global_id' => $datos['categoria_global_id'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? $categoria->descripcion,
            'estado' => isset($datos['estado']) ? $datos['estado'] : $categoria->estado,
        ]);

        return $categoria->fresh(); // Refrescar para obtener datos actualizados
    }

    /**
     * Elimina una categoría.
     *
     * @param int $id
     * @return bool
     */
    public function eliminar(int $id): bool
    {
        $categoria = $this->buscarPorId($id);
        
        // Validar si tiene productos asociados (incluso borrados lógicamente)
        if ($categoria->productos()->withTrashed()->exists()) {
            throw new \Exception("No se puede eliminar la categoría porque tiene productos asociados. Intente desactivarla en su lugar.");
        }
        
        return $categoria->delete();
    }

    /**
     * Obtiene las categorías globales activas para el combo.
     *
     * @return Collection
     */
    public function obtenerCategoriasGlobalesParaCombo(): Collection
    {
        return CategoriaGlobal::where('estado', true)
                              ->orderBy('nombre')
                              ->get();
    }

    /**
     * Cambia el estado de una categoría (toggle).
     *
     * @param int $id
     * @return Categoria
     */
    public function toggleEstado(int $id): Categoria
    {
        $categoria = $this->buscarPorId($id);
        $categoria->estado = !$categoria->estado;
        $categoria->save();
        
        return $categoria;
    }
}
