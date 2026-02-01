<?php

namespace App\Services;

use App\Models\Kardex;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Servicio para consultas del Kardex (Historial de Movimientos)
 */
class KardexService
{
    /**
     * Obtiene el historial de movimientos de un producto
     */
    public function obtenerPorProducto(int $productoId, ?string $fechaDesde = null, ?string $fechaHasta = null): Collection
    {
        $query = Kardex::where('producto_id', $productoId)
                       ->with('user')
                       ->orderBy('created_at', 'desc');

        if ($fechaDesde) {
            $query->whereDate('created_at', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $query->whereDate('created_at', '<=', $fechaHasta);
        }

        return $query->get();
    }

    /**
     * Obtiene todos los movimientos con filtros
     */
    public function obtenerTodos(array $filtros = []): LengthAwarePaginator
    {
        $query = Kardex::with(['producto', 'user'])
                       ->orderBy('created_at', 'desc');

        // Filtro por producto
        if (!empty($filtros['producto_id'])) {
            $query->where('producto_id', $filtros['producto_id']);
        }

        // Filtro por tipo de movimiento
        if (!empty($filtros['tipo_movimiento'])) {
            $query->where('tipo_movimiento', $filtros['tipo_movimiento']);
        }

        // Filtro por fechas
        if (!empty($filtros['fecha_desde'])) {
            $query->whereDate('created_at', '>=', $filtros['fecha_desde']);
        }

        if (!empty($filtros['fecha_hasta'])) {
            $query->whereDate('created_at', '<=', $filtros['fecha_hasta']);
        }

        // Filtro por referencia
        if (!empty($filtros['referencia_tipo'])) {
            $query->where('referencia_tipo', $filtros['referencia_tipo']);
        }

        $perPage = $filtros['per_page'] ?? 50;

        return $query->paginate($perPage);
    }

    /**
     * Obtiene el resumen de movimientos por tipo
     */
    public function resumenPorTipo(?string $fechaDesde = null, ?string $fechaHasta = null): array
    {
        $query = Kardex::select('tipo_movimiento', DB::raw('COUNT(*) as total'), DB::raw('SUM(cantidad) as cantidad_total'));

        if ($fechaDesde) {
            $query->whereDate('created_at', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $query->whereDate('created_at', '<=', $fechaHasta);
        }

        return $query->groupBy('tipo_movimiento')->get()->toArray();
    }

    /**
     * Obtiene los últimos movimientos para el dashboard
     */
    public function ultimosMovimientos(int $limite = 10): Collection
    {
        return Kardex::with(['producto', 'user'])
                     ->orderBy('created_at', 'desc')
                     ->limit($limite)
                     ->get();
    }

    /**
     * Tipos de movimiento disponibles
     */
    public static function getTiposMovimiento(): array
    {
        return [
            'VENTA' => 'Venta',
            'COMPRA' => 'Compra',
            'DEVOLUCION_CLIENTE' => 'Devolución Cliente',
            'DEVOLUCION_PROVEEDOR' => 'Devolución Proveedor',
            'INVENTARIO_INICIAL' => 'Inventario Inicial',
            'AJUSTE_ENTRADA' => 'Ajuste Entrada',
            'AJUSTE_SALIDA' => 'Ajuste Salida',
            'AJUSTE_POSITIVO' => 'Ajuste (+)',
            'AJUSTE_NEGATIVO' => 'Ajuste (-)',
            'TRANSFERENCIA' => 'Transferencia',
            'MERMA' => 'Merma',
            'CANCELACION' => 'Cancelación'
        ];
    }

    /**
     * Obtiene estadísticas del inventario
     */
    public function obtenerEstadisticas(): array
    {
        $hoy = now()->startOfDay();
        $inicioMes = now()->startOfMonth();

        return [
            'movimientos_hoy' => Kardex::whereDate('created_at', $hoy)->count(),
            'movimientos_mes' => Kardex::where('created_at', '>=', $inicioMes)->count(),
            'total_movimientos' => Kardex::count(),
            'entradas_mes' => Kardex::where('created_at', '>=', $inicioMes)
                                    ->whereIn('tipo_movimiento', ['COMPRA', 'AJUSTE_POSITIVO', 'AJUSTE_ENTRADA', 'DEVOLUCION_CLIENTE', 'INVENTARIO_INICIAL'])
                                    ->sum('cantidad'),
            'salidas_mes' => abs(Kardex::where('created_at', '>=', $inicioMes)
                                       ->whereIn('tipo_movimiento', ['VENTA', 'AJUSTE_NEGATIVO', 'AJUSTE_SALIDA', 'DEVOLUCION_PROVEEDOR', 'MERMA'])
                                       ->sum('cantidad')),
            'productos_stock_bajo' => Producto::where('estado', true)
                                              ->whereColumn('stock', '<=', 'stock_minimo')
                                              ->count(),
            'productos_sin_stock' => Producto::where('estado', true)
                                             ->where('stock', '<=', 0)
                                             ->count()
        ];
    }

    /**
     * Exporta el kardex a formato para Excel/PDF
     */
    public function exportar(int $productoId, ?string $fechaDesde = null, ?string $fechaHasta = null): array
    {
        $producto = Producto::with('unidad')->findOrFail($productoId);
        $movimientos = $this->obtenerPorProducto($productoId, $fechaDesde, $fechaHasta);

        return [
            'producto' => $producto,
            'movimientos' => $movimientos,
            'total_entradas' => $movimientos->where('cantidad', '>', 0)->sum('cantidad'),
            'total_salidas' => abs($movimientos->where('cantidad', '<', 0)->sum('cantidad')),
            'stock_actual' => $producto->stock
        ];
    }
}
