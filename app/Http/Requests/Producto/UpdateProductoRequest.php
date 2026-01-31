<?php

namespace App\Http\Requests\Producto;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
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
    $productoId = $this->route('producto'); // Obtiene el ID desde la URL

    return [
        'nombre' => 'required|string|max:200',
        'categoria_id' => 'required|exists:categorias,id',
        'marca_id' => 'required|exists:marcas,id',
        'unidad_id' => 'required|exists:unidades,id',
        'proveedor_id' => 'nullable|exists:proveedores,id',
        'precio_compra' => 'required|numeric|min:0',
        'precio_venta' => 'required|numeric|gte:precio_compra',
        'stock_minimo' => 'nullable|numeric|min:0',
        
        // Validación de unicidad ignorando el ID actual
        'codigo' => 'nullable|string|max:50|unique:productos,codigo,' . $productoId,
        'codigo_barras' => 'nullable|string|max:50|unique:productos,codigo_barras,' . $productoId,
        
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        'material' => 'nullable|string|max:100',
        'ubicacion' => 'nullable|string|max:100',
        'descripcion' => 'nullable|string',
    ];
}
}
