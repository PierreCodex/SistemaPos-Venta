<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Http\Resources\ProductoResource;
use Illuminate\Http\Request;

class ProductoApiController extends Controller
{
    /**
     * Muestra una lista de todos los productos.
     * 
     * CONCEPTO CLAVE: Pagination. En una API, nunca devuelvas miles de registros de golpe.
     * Usamos simplePaginate o paginate para enviar los datos por partes.
     */
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'marca', 'unidad'])
                         ->where('estado', 1);

        // Búsqueda inteligente para el Chatbot
        if ($request->has('buscar')) {
            $search = $request->get('buscar');
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('codigo', 'LIKE', "%{$search}%")
                  ->orWhere('codigo_barras', 'LIKE', "%{$search}%");
            });
        }

        $productos = $query->paginate(15);

        return ProductoResource::collection($productos);
    }

    /**
     * Muestra un producto específico.
     * 
     * CONCEPTO CLAVE: API Resources. Transforman el modelo en un formato JSON amigable.
     */
    public function show($id)
    {
        $producto = Producto::with(['categoria', 'marca', 'unidad'])->find($id);

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404); // Código 404: Not Found
        }

        return new ProductoResource($producto);
    }
}
