<?php

namespace App\Http\Requests\Presentacion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePresentacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // La presentación viene por route model binding
        $presentacion = $this->route('presentacion');

        return [
            'unidad_id' => 'required|exists:unidades,id',
            'factor' => 'required|numeric|gt:0',
            'precio_venta' => 'required|numeric|min:0',
            'codigo_barras' => [
                'nullable', 'string', 'max:50',
                // Único, pero ignorando la propia fila
                Rule::unique('producto_presentaciones', 'codigo_barras')
                    ->ignore($presentacion?->id),
            ],
            'estado' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'unidad_id.required' => 'Debe elegir la unidad de la presentación.',
            'factor.required' => 'El factor de conversión es obligatorio.',
            'factor.gt' => 'El factor debe ser mayor que cero.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'codigo_barras.unique' => 'Ese código de barras ya está registrado en otra presentación.',
        ];
    }
}
