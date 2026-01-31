<?php

namespace App\Http\Requests\Proveedor;

use Illuminate\Foundation\Http\FormRequest;

class StoreProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tipoDocumento = $this->input('tipo_documento');
        
        // Validación dinámica según tipo de documento
        $documentoRules = ['required', 'string', 'unique:proveedores,documento'];
        
        switch ($tipoDocumento) {
            case 'DNI':
                $documentoRules[] = 'size:8';
                $documentoRules[] = 'regex:/^[0-9]+$/';
                break;
            case 'RUC':
                $documentoRules[] = 'size:11';
                $documentoRules[] = 'regex:/^[0-9]+$/';
                break;
            case 'CE':
                $documentoRules[] = 'max:12';
                break;
        }

        return [
            'tipo_documento' => 'required|in:DNI,RUC,CE',
            'documento' => $documentoRules,
            'nombre' => 'required|string|max:200',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_documento.required' => 'El tipo de documento es obligatorio.',
            'tipo_documento.in' => 'El tipo de documento debe ser DNI, RUC o CE.',
            'documento.required' => 'El número de documento es obligatorio.',
            'documento.unique' => 'Este número de documento ya está registrado.',
            'documento.size' => 'El :attribute debe tener exactamente :size caracteres.',
            'documento.regex' => 'El documento solo debe contener números.',
            'documento.max' => 'El documento no debe exceder :max caracteres.',
            'nombre.required' => 'El nombre o razón social es obligatorio.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'documento' => 'número de documento',
        ];
    }
}
