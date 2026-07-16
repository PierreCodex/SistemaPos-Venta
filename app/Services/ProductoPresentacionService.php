<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\ProductoPresentacion;
use Illuminate\Database\Eloquent\Collection;

/**
 * Lógica de negocio de las presentaciones de un producto.
 *
 * Las reglas duras (factor inmutable si hay ventas, base única, no borrar la
 * base) viven en el modelo ProductoPresentacion para que valgan desde
 * cualquier vía. Este servicio añade las validaciones propias de la edición
 * por pantalla y traduce las excepciones del modelo en mensajes claros.
 */
class ProductoPresentacionService
{
    /**
     * Presentaciones de un producto, con la base primero.
     */
    public function listar(Producto $producto): Collection
    {
        return $producto->presentaciones()
            ->with('unidad')
            ->orderByDesc('es_base')
            ->orderBy('factor')
            ->get();
    }

    /**
     * Crea una presentación no-base para el producto.
     */
    public function crear(Producto $producto, array $datos): ProductoPresentacion
    {
        $this->validarUnidadLibre($producto, (int) $datos['unidad_id']);

        return $producto->presentaciones()->create([
            'unidad_id'     => $datos['unidad_id'],
            'factor'        => $datos['factor'],
            'precio_venta'  => $datos['precio_venta'],
            'codigo_barras' => $datos['codigo_barras'] ?? null,
            'es_base'       => false,
            'estado'        => $datos['estado'] ?? true,
        ]);
    }

    /**
     * Actualiza una presentación.
     *
     * La base solo admite cambio de precio y código: su unidad y su factor (1)
     * definen la unidad del stock y se gestionan desde el producto. El factor
     * de una presentación ya usada lo bloquea el modelo.
     */
    public function actualizar(ProductoPresentacion $presentacion, array $datos): ProductoPresentacion
    {
        if ($presentacion->es_base) {
            $presentacion->update([
                'precio_venta'  => $datos['precio_venta'],
                'codigo_barras' => $datos['codigo_barras'] ?? null,
            ]);

            return $presentacion->fresh('unidad');
        }

        if (isset($datos['unidad_id']) && (int) $datos['unidad_id'] !== $presentacion->unidad_id) {
            $this->validarUnidadLibre($presentacion->producto, (int) $datos['unidad_id'], $presentacion->id);
        }

        $presentacion->update([
            'unidad_id'     => $datos['unidad_id'] ?? $presentacion->unidad_id,
            'factor'        => $datos['factor'] ?? $presentacion->factor,
            'precio_venta'  => $datos['precio_venta'],
            'codigo_barras' => $datos['codigo_barras'] ?? null,
            'estado'        => $datos['estado'] ?? $presentacion->estado,
        ]);

        return $presentacion->fresh('unidad');
    }

    /**
     * Activa o desactiva una presentación.
     *
     * La base no se puede desactivar: es la unidad en la que se lleva el stock.
     */
    public function toggleEstado(ProductoPresentacion $presentacion): ProductoPresentacion
    {
        if ($presentacion->es_base) {
            throw new \Exception('La presentación base no se puede desactivar: es la unidad en la que se mide el stock.');
        }

        $presentacion->estado = !$presentacion->estado;
        $presentacion->save();

        return $presentacion;
    }

    /**
     * Elimina una presentación. El modelo impide borrar la base o una
     * presentación con movimientos.
     */
    public function eliminar(ProductoPresentacion $presentacion): bool
    {
        return (bool) $presentacion->delete();
    }

    /**
     * Un producto no puede tener dos presentaciones en la misma unidad
     * (choca con el índice único). Se valida antes para dar un mensaje claro
     * en vez de un error SQL.
     */
    private function validarUnidadLibre(Producto $producto, int $unidadId, ?int $exceptoId = null): void
    {
        $existe = $producto->presentaciones()
            ->where('unidad_id', $unidadId)
            ->when($exceptoId, fn ($q) => $q->where('id', '!=', $exceptoId))
            ->exists();

        if ($existe) {
            throw new \Exception('El producto ya tiene una presentación en esa unidad. Elija otra unidad.');
        }
    }
}
