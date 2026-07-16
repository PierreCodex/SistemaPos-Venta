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
                $producto = Producto::findOrFail($item['producto_id']);

                // Al proveedor se le compra por caja, no por unidad: es aquí
                // donde la presentación se usa más. Sin presentacion_id se
                // asume la base (factor 1) y todo se comporta como antes.
                $presentacion = $producto->resolverPresentacion($item['presentacion_id'] ?? null);

                $cantidad = (float) $item['cantidad'];

                if (!$presentacion->permiteDecimales() && floor($cantidad) != $cantidad) {
                    throw new \Exception("El producto '{$producto->nombre}' no admite cantidades decimales en {$presentacion->unidad->codigo}.");
                }

                $cantidadBase = $presentacion->aBase($cantidad);
                $factor = (float) $presentacion->factor;

                // El costo ingresado es POR PRESENTACIÓN (S/ 90 la caja).
                // precio_compra y la valorización del kardex se llevan por
                // unidad base, así que hay que dividirlo entre el factor;
                // guardarlo tal cual haría creer que cada unidad cuesta 90.
                $costoPresentacion = (float) $item['costo_unitario'];
                $costoBase = $factor > 0 ? round($costoPresentacion / $factor, 2) : $costoPresentacion;

                // Crear detalle (con el snapshot del factor)
                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $item['producto_id'],
                    'presentacion_id' => $presentacion->id,
                    'cantidad' => $cantidad,
                    'factor_aplicado' => $presentacion->factor,
                    'cantidad_base' => $cantidadBase,
                    'costo_unitario' => $costoPresentacion,
                    'descuento' => $item['descuento'] ?? 0,
                    'subtotal' => $item['subtotal'],
                    'fecha_vencimiento' => $item['fecha_vencimiento'] ?? null,
                    'lote' => $item['lote'] ?? null
                ]);

                // Actualizar stock del producto (en unidad base)
                $stockAnterior = $producto->stock;
                $stockNuevo = $stockAnterior + $cantidadBase;

                $producto->update([
                    'stock' => $stockNuevo,
                    'precio_compra' => $costoBase // Último costo, por unidad base
                ]);

                // Registrar en Kardex (cantidad y costo SIEMPRE en unidad base)
                Kardex::create([
                    'producto_id' => $item['producto_id'],
                    'presentacion_id' => $presentacion->id,
                    'tipo_movimiento' => 'COMPRA',
                    'referencia_tipo' => 'compras',
                    'referencia_id' => $compra->id,
                    'cantidad' => $cantidadBase,
                    'cantidad_presentacion' => $cantidad,
                    'costo_unitario' => $costoBase,
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
     * Cantidad en unidad base que ingresó una línea de compra.
     *
     * Usa el snapshot de la compra, nunca el factor vigente del catálogo.
     * El cálculo con factor_aplicado es una red de seguridad para filas
     * sin cantidad_base (el backfill la pobló en todas).
     */
    private function cantidadBaseDe(DetalleCompra $detalle): float
    {
        $cantidadBase = (float) $detalle->cantidad_base;

        if ($cantidadBase > 0) {
            return $cantidadBase;
        }

        $factor = (float) $detalle->factor_aplicado ?: 1.0;

        return round((float) $detalle->cantidad * $factor, 3);
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

                // Se retira lo que esta línea ingresó, con el factor congelado
                // en la compra, no con el vigente.
                $cantidadBase = $this->cantidadBaseDe($detalle);

                $stockAnterior = $producto->stock;
                $stockNuevo = $stockAnterior - $cantidadBase;

                $producto->update(['stock' => max(0, $stockNuevo)]);

                // Registrar en Kardex
                Kardex::create([
                    'producto_id' => $detalle->producto_id,
                    'presentacion_id' => $detalle->presentacion_id,
                    'tipo_movimiento' => 'DEVOLUCION_PROVEEDOR',
                    'referencia_tipo' => 'compras',
                    'referencia_id' => $compra->id,
                    'cantidad' => -$cantidadBase,
                    'cantidad_presentacion' => -$detalle->cantidad,
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
