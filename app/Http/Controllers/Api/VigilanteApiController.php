<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VigilanteApiController extends Controller
{
    /**
     * Unidades vendidas de un producto en un rango de tiempo.
     * Lo consulta VigilanteIA para clasificar una baja de stock físico:
     * hubo venta = baja legítima; no hubo = posible merma/robo.
     */
    public function ventas(Request $request)
    {
        $request->validate([
            'sku'   => 'required|string|max:120',
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:desde',
        ]);

        $sku = $request->get('sku');
        $producto = Producto::withTrashed()
            ->where('codigo_barras', $sku)
            ->orWhere('codigo', $sku)
            ->first();

        if (!$producto) {
            return response()->json([
                'sku' => $sku,
                'unidades_vendidas' => 0,
                'producto_encontrado' => false,
            ]);
        }

        $unidades = DetalleVenta::where('producto_id', $producto->id)
            ->whereHas('venta', function ($q) use ($request) {
                $q->whereBetween('fecha_emision', [
                        Carbon::parse($request->get('desde')),
                        Carbon::parse($request->get('hasta')),
                    ])
                  ->whereNull('fecha_anulacion'); // las anuladas no justifican bajas
            })
            ->sum('cantidad');

        return response()->json([
            'sku' => $sku,
            'unidades_vendidas' => (float) $unidades,
            'producto_encontrado' => true,
        ]);
    }

    /**
     * Stock teórico actual de un producto (OPCIONAL — mejora el panel:
     * "teórico vs observado por la cámara").
     */
    public function stock(Request $request)
    {
        $request->validate(['sku' => 'required|string|max:120']);
        $sku = $request->get('sku');

        $producto = Producto::where('codigo_barras', $sku)
            ->orWhere('codigo', $sku)
            ->first();

        if (!$producto) {
            return response()->json([
                'sku' => $sku,
                'producto_encontrado' => false,
            ], 200);
        }

        return response()->json([
            'sku' => $sku,
            'nombre' => $producto->nombre,
            'stock' => (float) $producto->stock,
            'producto_encontrado' => true,
        ]);
    }
}
