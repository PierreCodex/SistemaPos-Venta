<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Kardex;
use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Servicio para la lógica de negocio de Compras
 */
class CompraService
{
    /**
     * Obtiene todas las compras con relaciones
     */
    public function obtenerTodas(): Collection
    {
        return Compra::with(['proveedor', 'user', 'detalles.producto'])
                     ->orderBy('created_at', 'desc')
                     ->get();
    }

    /**
     * Busca una compra por ID
     */
    public function buscarPorId(int $id): Compra
    {
        return Compra::with(['proveedor', 'user', 'detalles.producto.unidad'])
                     ->findOrFail($id);
    }

    /**
     * Crea una nueva compra y actualiza el stock
     */
    public function crear(array $datos): Compra
    {
        return DB::transaction(function () use ($datos) {
            // 1. Crear la compra
            $compra = Compra::create([
                'proveedor_id' => $datos['proveedor_id'],
                'user_id' => auth()->id(),
                'tipo_comprobante' => $datos['tipo_comprobante'],
                'numero_comprobante' => $datos['numero_comprobante'],
                'fecha_emision' => $datos['fecha_emision'],
                'fecha_vencimiento' => $datos['fecha_vencimiento'] ?? null,
                'subtotal' => $datos['subtotal'],
                'igv' => $datos['igv'] ?? 0,
                'descuento' => $datos['descuento'] ?? 0,
                'total' => $datos['total'],
                'forma_pago' => $datos['forma_pago'] ?? 'CONTADO',
                'estado_pago' => $datos['forma_pago'] === 'CONTADO' ? 'PAGADO' : 'PENDIENTE',
                'monto_pagado' => $datos['forma_pago'] === 'CONTADO' ? $datos['total'] : ($datos['monto_pagado'] ?? 0),
                'observaciones' => $datos['observaciones'] ?? null,
                'estado' => 'COMPLETADO'
            ]);

            // 2. Crear los detalles y actualizar stock
            foreach ($datos['productos'] as $item) {
                // Crear detalle
                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'costo_unitario' => $item['costo_unitario'],
                    'descuento' => $item['descuento'] ?? 0,
                    'subtotal' => $item['subtotal'],
                    'fecha_vencimiento' => $item['fecha_vencimiento'] ?? null,
                    'lote' => $item['lote'] ?? null
                ]);

                // Actualizar stock del producto
                $producto = Producto::findOrFail($item['producto_id']);
                $stockAnterior = $producto->stock;
                $stockNuevo = $stockAnterior + $item['cantidad'];
                
                $producto->update([
                    'stock' => $stockNuevo,
                    'precio_compra' => $item['costo_unitario'] // Actualizar último costo
                ]);

                // Registrar en Kardex
                Kardex::create([
                    'producto_id' => $item['producto_id'],
                    'tipo_movimiento' => 'COMPRA',
                    'referencia_tipo' => 'compras',
                    'referencia_id' => $compra->id,
                    'cantidad' => $item['cantidad'],
                    'costo_unitario' => $item['costo_unitario'],
                    'stock_anterior' => $stockAnterior,
                    'stock_resultante' => $stockNuevo,
                    'user_id' => auth()->id(),
                    'observaciones' => "Compra #{$compra->numero_comprobante}"
                ]);
            }

            return $compra->fresh(['proveedor', 'detalles.producto']);
        });
    }

    /**
     * Anula una compra y revierte el stock
     */
    public function anular(int $id): Compra
    {
        return DB::transaction(function () use ($id) {
            $compra = Compra::with('detalles')->findOrFail($id);

            if ($compra->estado === 'ANULADO') {
                throw new \Exception('Esta compra ya fue anulada.');
            }

            // Revertir stock de cada producto
            foreach ($compra->detalles as $detalle) {
                $producto = Producto::findOrFail($detalle->producto_id);
                $stockAnterior = $producto->stock;
                $stockNuevo = $stockAnterior - $detalle->cantidad;

                $producto->update(['stock' => max(0, $stockNuevo)]);

                // Registrar en Kardex
                Kardex::create([
                    'producto_id' => $detalle->producto_id,
                    'tipo_movimiento' => 'DEVOLUCION_PROVEEDOR',
                    'referencia_tipo' => 'compras',
                    'referencia_id' => $compra->id,
                    'cantidad' => -$detalle->cantidad,
                    'costo_unitario' => $detalle->costo_unitario,
                    'stock_anterior' => $stockAnterior,
                    'stock_resultante' => max(0, $stockNuevo),
                    'user_id' => auth()->id(),
                    'observaciones' => "Anulación Compra #{$compra->numero_comprobante}"
                ]);
            }

            $compra->update(['estado' => 'ANULADO']);

            return $compra->fresh();
        });
    }

    /**
     * Obtiene proveedores para el combo
     */
    public function obtenerProveedoresParaCombo(): Collection
    {
        return Proveedor::where('estado', true)
                        ->orderBy('nombre')
                        ->get(['id', 'nombre', 'documento']);
    }

    /**
     * Busca productos para agregar a la compra
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
     * Estadísticas de compras
     */
    public function obtenerEstadisticas(): array
    {
        $hoy = now()->startOfDay();
        $inicioMes = now()->startOfMonth();

        return [
            'compras_hoy' => Compra::whereDate('created_at', $hoy)->completadas()->sum('total'),
            'compras_mes' => Compra::where('created_at', '>=', $inicioMes)->completadas()->sum('total'),
            'total_compras' => Compra::completadas()->count(),
            'pendientes_pago' => Compra::pendientesPago()->sum(DB::raw('total - monto_pagado'))
        ];
    }
}
