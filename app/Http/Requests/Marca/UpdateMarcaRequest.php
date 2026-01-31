<?php

namespace App\Http\Requests\Marca;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMarcaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtener el ID del registro actual desde la ruta
        $id = $this->route('marca');
        
        return [
            'codigo' => [
                'required',
                'string',
                'min:2',
                'max:20',
                'regex:/^[a-zA-Z0-9\-]+$/',
                'unique:marcas,codigo,' . $id,
            ],
            'nombre' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ0-9\s\-\.]+$/',
                'unique:marcas,nombre,' . $id,
            ],
            'descripcion' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.min' => 'El código debe tener mínimo 2 caracteres.',
            'codigo.regex' => 'El código solo puede contener letras, números y guiones.',
            'codigo.unique' => 'Este código ya está registrado.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.min' => 'El nombre debe tener mínimo 2 caracteres.',
            'nombre.regex' => 'El nombre contiene caracteres no permitidos.',
            'nombre.unique' => 'Este nombre ya está registrado.',
            'descripcion.max' => 'La descripción no puede exceder 255 caracteres.',
        ];
    }
}
