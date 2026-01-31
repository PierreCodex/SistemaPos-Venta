<?php

namespace App\Services;

use App\Models\CategoriaGlobal;
use Illuminate\Database\Eloquent\Collection;

/**
 * Servicio para la lógica de negocio de Categorías Globales.
 * 
 * Este servicio encapsula toda la lógica de negocio relacionada con
 * las categorías globales, siguiendo el principio de separación de
 * responsabilidades (SoC).
 * 
 * @package App\Services
 */
class CategoriaGlobalService
{
    /**
     * Obtiene todas las categorías globales ordenadas por nombre.
     *
     * @return Collection
     */
    public function obtenerTodas(): Collection
    {
        return CategoriaGlobal::orderBy('nombre')->get();
    }

    /**
     * Obtiene solo las categorías globales activas.
     *
     * @return Collection
     */
    public function obtenerActivas(): Collection
    {
        return CategoriaGlobal::where('estado', true)
                              ->orderBy('nombre')
                              ->get();
    }

    /**
     * Busca una categoría global por su ID.
     *
     * @param int $id
     * @return CategoriaGlobal
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function buscarPorId(int $id): CategoriaGlobal
    {
        return CategoriaGlobal::findOrFail($id);
    }

    /**
     * Crea una nueva categoría global.
     *
     * @param array $datos
     * @return CategoriaGlobal
     */
    public function crear(array $datos): CategoriaGlobal
    {
        return CategoriaGlobal::create([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'estado' => $datos['estado'] ?? true,
        ]);
    }

    /**
     * Actualiza una categoría global existente.
     *
     * @param int $id
     * @param array $datos
     * @return CategoriaGlobal
     */
    public function actualizar(int $id, array $datos): CategoriaGlobal
    {
        $categoriaGlobal = $this->buscarPorId($id);
        
        $categoriaGlobal->update([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? $categoriaGlobal->descripcion,
            'estado' => isset($datos['estado']) ? $datos['estado'] : $categoriaGlobal->estado,
        ]);

        return $categoriaGlobal->fresh();
    }

    /**
     * Elimina una categoría global.
     *
     * @param int $id
     * @return bool
     */
    public function eliminar(int $id): bool
    {
        $categoriaGlobal = $this->buscarPorId($id);
        
        // No permitir eliminar si tiene subcategorías
        if ($categoriaGlobal->categorias()->exists()) {
            throw new \Exception("No se puede eliminar la categoría global porque tiene subcategorías asociadas. Primero elimine las subcategorías.");
        }
        
        return $categoriaGlobal->delete();
    }

    /**
     * Verifica si una categoría global puede ser eliminada.
     * No debe tener subcategorías asociadas.
     *
     * @param int $id
     * @return bool
     */
    public function puedeEliminarse(int $id): bool
    {
        $categoriaGlobal = $this->buscarPorId($id);
        return $categoriaGlobal->categorias()->count() === 0;
    }

    /**
     * Cambia el estado de una categoría global (toggle).
     *
     * @param int $id
     * @return CategoriaGlobal
     */
    public function toggleEstado(int $id): CategoriaGlobal
    {
        $categoriaGlobal = $this->buscarPorId($id);
        $categoriaGlobal->estado = !$categoriaGlobal->estado;
        $categoriaGlobal->save();
        
        return $categoriaGlobal;
    }
}

