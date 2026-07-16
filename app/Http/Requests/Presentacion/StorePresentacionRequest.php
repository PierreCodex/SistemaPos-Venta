<?php

namespace App\Http\Requests\Presentacion;

use Illuminate\Foundation\Http\FormRequest;

class StorePresentacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unidad_id' => 'required|exists:unidades,id',
            // El factor es cuántas unidades base contiene. Debe ser > 0;
            // se admite decimal para casos como "bolsa de 0.5 kg" cuando la
            // base es el kilo.
            'factor' => 'required|numeric|gt:0',
            'precio_venta' => 'required|numeric|min:0',
            // El código de barras de la caja, si trae uno propio. Único en
            // toda la tabla para que el escáner lo resuelva sin ambigüedad.
            'codigo_barras' => 'nullable|string|max:50|unique:producto_presentaciones,codigo_barras',
            'estado' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'unidad_id.required' => 'Debe elegir la unidad de la presentación.',
            'unidad_id.exists' => 'La unidad seleccionada no existe.',
            'factor.required' => 'El factor de conversión es obligatorio.',
            'factor.gt' => 'El factor debe ser mayor que cero.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'codigo_barras.unique' => 'Ese código de barras ya está registrado en otra presentación.',
        ];
    }
}
