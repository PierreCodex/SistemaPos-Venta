<?php

namespace App\Http\Requests\CategoriaGlobal;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para validar la creación de una Categoría Global.
 * 
 * Validaciones:
 * - El nombre no puede estar duplicado en categorías globales
 * - No permite caracteres especiales peligrosos
 * - Mínimo 2 caracteres para el nombre
 */
class StoreCategoriaGlobalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s\-\.]+$/',
                'unique:categorias_globales,nombre',
            ],
            'descripcion' => 'nullable|string|max:500',
            'estado' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras, números, espacios, guiones y puntos.',
            'nombre.unique' => 'Ya existe una categoría global con este nombre.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre de categoría global',
            'descripcion' => 'descripción',
        ];
    }
}
