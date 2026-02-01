<?php

namespace App\Services;

use App\Models\AjusteInventario;
use App\Models\DetalleAjusteInventario;
use App\Models\Producto;
use App\Models\Kardex;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Servicio para la lógica de negocio de Ajustes de Inventario
 */
class AjusteInventarioService
{
    /**
     * Obtiene todos los ajustes con relaciones
     */
    public function obtenerTodos(): Collection
    {
        return AjusteInventario::with(['user', 'detalles.producto'])
                               ->orderBy('created_at', 'desc')
                               ->get();
    }

    /**
     * Busca un ajuste por ID
     */
    public function buscarPorId(int $id): AjusteInventario
    {
        return AjusteInventario::with(['user', 'detalles.producto.unidad'])
                               ->findOrFail($id);
    }

    /**
     * Crea un nuevo ajuste de inventario
     */
    public function crear(array $datos): AjusteInventario
    {
        return DB::transaction(function () use ($datos) {
            // 1. Crear el ajuste
            $ajuste = AjusteInventario::create([
                'tipo' => $datos['tipo'],
                'motivo' => $datos['motivo'],
                'descripcion' => $datos['descripcion'] ?? null,
                'user_id' => auth()->id(),
                'fecha' => $datos['fecha'] ?? now(),
                'estado' => $datos['aplicar_ahora'] ?? false ? 'APLICADO' : 'BORRADOR'
            ]);

            // 2. Crear los detalles
            foreach ($datos['productos'] as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                $stockSistema = $producto->stock;
                $stockFisico = $item['stock_fisico'];
                $diferencia = $stockFisico - $stockSistema;

                DetalleAjusteInventario::create([
                    'ajuste_id' => $ajuste->id,
                    'producto_id' => $item['producto_id'],
                    'stock_sistema' => $stockSistema,
                    'stock_fisico' => $stockFisico,
                    'diferencia' => $diferencia,
                    'observacion' => $item['observacion'] ?? null
                ]);
            }

            // 3. Si se debe aplicar ahora, actualizar stock
            if ($datos['aplicar_ahora'] ?? false) {
                $this->aplicar($ajuste->id);
            }

            return $ajuste->fresh(['user', 'detalles.producto']);
        });
    }

    /**
     * Aplica un ajuste de inventario (actualiza el stock)
     */
    public function aplicar(int $id): AjusteInventario
    {
        return DB::transaction(function () use ($id) {
            $ajuste = AjusteInventario::with('detalles')->findOrFail($id);

            if ($ajuste->estado === 'APLICADO') {
                throw new \Exception('Este ajuste ya fue aplicado.');
            }

            if ($ajuste->estado === 'ANULADO') {
                throw new \Exception('No se puede aplicar un ajuste anulado.');
            }

            foreach ($ajuste->detalles as $detalle) {
                $producto = Producto::findOrFail($detalle->producto_id);
                $stockAnterior = $producto->stock;
                
                // El stock nuevo es el stock físico registrado
                $stockNuevo = $detalle->stock_fisico;
                $diferencia = $detalle->diferencia;

                // Actualizar stock del producto
                $producto->update(['stock' => $stockNuevo]);

                // Determinar tipo de movimiento en Kardex
                $tipoMovimiento = $diferencia >= 0 ? 'AJUSTE_POSITIVO' : 'AJUSTE_NEGATIVO';

                // Registrar en Kardex
                Kardex::create([
                    'producto_id' => $detalle->producto_id,
                    'tipo_movimiento' => $tipoMovimiento,
                    'referencia_tipo' => 'ajustes_inventario',
                    'referencia_id' => $ajuste->id,
                    'cantidad' => $diferencia,
                    'stock_anterior' => $stockAnterior,
                    'stock_resultante' => $stockNuevo,
                    'user_id' => auth()->id(),
                    'observaciones' => "Ajuste #{$ajuste->id} - Motivo: {$ajuste->motivo}"
                ]);
            }

            $ajuste->update(['estado' => 'APLICADO']);

            return $ajuste->fresh(['detalles.producto']);
        });
    }

    /**
     * Anula un ajuste de inventario (revierte el stock)
     */
    public function anular(int $id): AjusteInventario
    {
        return DB::transaction(function () use ($id) {
            $ajuste = AjusteInventario::with('detalles')->findOrFail($id);

            if ($ajuste->estado !== 'APLICADO') {
                throw new \Exception('Solo se pueden anular ajustes aplicados.');
            }

            foreach ($ajuste->detalles as $detalle) {
                $producto = Producto::findOrFail($detalle->producto_id);
                $stockActual = $producto->stock;
                
                // Revertir: el stock vuelve al stock_sistema original
                $stockRevertido = $detalle->stock_sistema;
                $diferenciaReversion = $stockRevertido - $stockActual;

                $producto->update(['stock' => $stockRevertido]);

                // Registrar reversión en Kardex
                $tipoMovimiento = $diferenciaReversion >= 0 ? 'AJUSTE_POSITIVO' : 'AJUSTE_NEGATIVO';

                Kardex::create([
                    'producto_id' => $detalle->producto_id,
                    'tipo_movimiento' => $tipoMovimiento,
                    'referencia_tipo' => 'ajustes_inventario',
                    'referencia_id' => $ajuste->id,
                    'cantidad' => $diferenciaReversion,
                    'stock_anterior' => $stockActual,
                    'stock_resultante' => $stockRevertido,
                    'user_id' => auth()->id(),
                    'observaciones' => "ANULACIÓN Ajuste #{$ajuste->id}"
                ]);
            }

            $ajuste->update(['estado' => 'ANULADO']);

            return $ajuste->fresh();
        });
    }

    /**
     * Elimina un ajuste en borrador
     */
    public function eliminar(int $id): bool
    {
        $ajuste = AjusteInventario::findOrFail($id);

        if ($ajuste->estado !== 'BORRADOR') {
            throw new \Exception('Solo se pueden eliminar ajustes en borrador.');
        }

        return $ajuste->delete();
    }

    /**
     * Busca productos para el ajuste
     */
    public function buscarProductos(string $termino): Collection
    {
        return Producto::where('estado', true)
            ->where(function ($q) use ($termino) {
                $q->where('nombre', 'like', "%{$termino}%")
                  ->orWhere('codigo', 'like', "%{$termino}%")
                  ->orWhere('codigo_barras', 'like', "%{$termino}%");
            })
            ->with(['unidad', 'categoria'])
            ->limit(20)
            ->get();
    }

    /**
     * Obtiene productos con stock bajo
     */
    public function productosStockBajo(): Collection
    {
        return Producto::where('estado', true)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->with(['unidad', 'categoria'])
            ->orderBy('stock')
            ->get();
    }
}
