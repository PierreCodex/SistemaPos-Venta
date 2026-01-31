<?php

namespace App\Http\Requests\Categoria;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request para validar la creación de una Categoría (Subcategoría).
 * 
 * Validaciones:
 * - El nombre no puede estar duplicado dentro de la misma categoría global
 * - La categoría global debe existir y estar activa
 * - No permite caracteres especiales peligrosos
 */
class StoreCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categoria_global_id' => [
                'required',
                'integer',
                Rule::exists('categorias_globales', 'id')->where(function ($query) {
                    // Solo permitir categorías globales activas
                    $query->where('estado', true);
                }),
            ],
            'nombre' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s\-\.]+$/', // Solo alfanuméricos y algunos caracteres
                // Validación unique compuesta: nombre + categoria_global_id
                Rule::unique('categorias')->where(function ($query) {
                    return $query->where('categoria_global_id', $this->categoria_global_id);
                }),
            ],
            'descripcion' => 'nullable|string|max:500',
            'estado' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'categoria_global_id.required' => 'Debe seleccionar una categoría global.',
            'categoria_global_id.exists' => 'La categoría global seleccionada no existe o está inactiva.',
            'categoria_global_id.integer' => 'La categoría global debe ser válida.',
            
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras, números, espacios, guiones y puntos.',
            'nombre.unique' => 'Ya existe una categoría con este nombre en la categoría global seleccionada.',
            
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'categoria_global_id' => 'categoría global',
            'nombre' => 'nombre de categoría',
            'descripcion' => 'descripción',
        ];
    }
}
