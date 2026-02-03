<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'codigo_barras' => $this->codigo_barras,
            'nombre' => $this->nombre,
            'existencias' => [
                'actual' => $this->stock,
                'minimo' => $this->stock_minimo,
                'maximo' => $this->stock_maximo,
            ],
            'precios' => [
                'venta' => (float) $this->precio_venta,
                'mayorista' => (float) $this->precio_mayorista,
                'compra' => (float) $this->precio_compra, // Cuidado: solo si es admin/autenticado
            ],
            'categoria' => $this->whenLoaded('categoria', function() {
                return $this->categoria->nombre;
            }),
            'marca' => $this->whenLoaded('marca', function() {
                return $this->marca->nombre;
            }),
            'unidad' => $this->whenLoaded('unidad', function() {
                return $this->unidad->nombre;
            }),
            'web_url' => route('productos.show', $this->id),
            'imagen_url' => $this->imagen_url ?? asset('images/default-product.png'),
            'estado' => $this->estado,
        ];
    }
}
