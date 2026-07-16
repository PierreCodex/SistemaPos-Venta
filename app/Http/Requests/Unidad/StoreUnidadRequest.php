<?php

namespace App\Http\Requests\Unidad;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => [
                'required',
                'string',
                'min:1',
                'max:10',
                'regex:/^[A-Za-z0-9]+$/',
                'unique:unidades,codigo',
            ],
            'nombre' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'unique:unidades,nombre',
            ],
            'descripcion' => 'nullable|string|max:255',
            // Decide si la unidad admite cantidades fraccionadas (1.5 KG sí,
            // 1.5 cajas no). Sin esta regla nunca llegaba al servicio y toda
            // unidad nueva quedaba en false por el default de la migración.
            'permite_decimales' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.max' => 'El código no puede exceder 10 caracteres.',
            'codigo.regex' => 'El código solo puede contener letras y números.',
            'codigo.unique' => 'Este código ya está registrado.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener mínimo 2 caracteres.',
            'nombre.unique' => 'Este nombre ya está registrado.',
            'descripcion.max' => 'La descripción no puede exceder 255 caracteres.',
        ];
    }
}
