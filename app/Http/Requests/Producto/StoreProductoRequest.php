<?php

namespace App\Http\Requests\Producto;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:200',
            'categoria_id' => 'required|exists:categorias,id',
            'marca_id' => 'required|exists:marcas,id',
            'unidad_id' => 'required|exists:unidades,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            
            // Precios y Stock
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|gte:precio_compra',
            'stock_inicial' => 'required|numeric|min:0', // Importante para el Kardex inicial
            'stock_minimo' => 'nullable|numeric|min:0',
            
            // Códigos e Imagen
            'codigo' => 'nullable|string|max:50|unique:productos,codigo', // SKU único
            'codigo_barras' => 'nullable|string|max:50|unique:productos,codigo_barras',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240', // 10MB, incluyendo webp
            
            // Adicionales de tu modal
            'material' => 'nullable|string|max:100', // Opcional para tiendas de abarrotes
            'ubicacion' => 'nullable|string|max:100',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:today',
            'descripcion' => 'nullable|string',
        ];
    }
}
