<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class ConfiguracionController extends Controller
{
    /**
     * Mostrar la página de configuración
     */
    public function index()
    {
        $empresa = Empresa::first() ?? new Empresa();
        
        return view('configuracion.index', compact('empresa'));
    }

    /**
     * Actualizar la configuración general
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'razon_social' => 'required|string|max:200',
            'nombre_comercial' => 'nullable|string|max:200',
            'ruc' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'igv_porcentaje' => 'required|numeric|min:0|max:100',
            'moneda' => 'required|string|max:10',
            // Nuevos campos SUNAT
            'sunat_sol_user' => 'nullable|string|max:50',
            'sunat_sol_pass' => 'nullable|string|max:100',
            'sunat_client_id' => 'nullable|string|max:200',
            'sunat_client_secret' => 'nullable|string|max:200',
            'sunat_produccion' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $empresa = Empresa::first() ?? new Empresa();
        
        $empresa->fill($request->only([
            'razon_social',
            'nombre_comercial',
            'ruc',
            'direccion',
            'telefono',
            'email',
            'igv_porcentaje',
            'moneda',
            'sunat_sol_user',
            'sunat_sol_pass',
            'sunat_client_id',
            'sunat_client_secret',
        ]));

        // Manejar el checkbox de producción
        $empresa->sunat_produccion = $request->boolean('sunat_produccion');

        $empresa->save();
        
        // Limpiar caché
        Cache::forget('empresa_config');

        return response()->json([
            'success' => true,
            'message' => 'Configuración actualizada correctamente'
        ]);
    }

    /**
     * Subir el certificado digital (.pem)
     */
    public function uploadCertificado(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'certificado' => 'required|file|max:1024' // máx 1MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Archivo inválido. Debe subir un archivo de certificado válido.',
            ], 422);
        }

        $empresa = Empresa::first() ?? new Empresa();

        // Guardar certificado en storage/app/public/certificados
        $file = $request->file('certificado');
        $filename = 'cert_' . $empresa->ruc . '_' . time() . '.' . $file->getClientOriginalExtension();
        
        // Crear directorio si no existe
        if (!Storage::disk('public')->exists('certificados')) {
            Storage::disk('public')->makeDirectory('certificados');
        }

        $path = $file->storeAs('certificados', $filename, 'public');

        // Eliminar anterior si existe
        if ($empresa->sunat_cert_path && Storage::disk('public')->exists('certificados/' . $empresa->sunat_cert_path)) {
            Storage::disk('public')->delete('certificados/' . $empresa->sunat_cert_path);
        }

        $empresa->sunat_cert_path = $filename;
        $empresa->save();

        Cache::forget('empresa_config');

        return response()->json([
            'success' => true,
            'message' => 'Certificado digital subido correctamente',
            'filename' => $filename
        ]);
    }

    /**
     * Subir el logo de la empresa
     */
    public function uploadLogo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Archivo inválido. Debe ser una imagen (JPEG, PNG, JPG, GIF, SVG, WEBP) de máximo 2MB.',
                'errors' => $validator->errors()
            ], 422);
        }

        $empresa = Empresa::first();
        
        if (!$empresa) {
            $empresa = new Empresa();
            $empresa->razon_social = 'Mi Empresa';
            $empresa->igv_porcentaje = 18;
            $empresa->moneda = 'PEN';
        }

        // Eliminar logo anterior si existe
        if ($empresa->logo && Storage::disk('public')->exists($empresa->logo)) {
            Storage::disk('public')->delete($empresa->logo);
        }

        // Guardar nuevo logo
        $path = $request->file('logo')->store('logos', 'public');
        $empresa->logo = $path;
        $empresa->save();

        // Limpiar caché
        Cache::forget('empresa_config');

        return response()->json([
            'success' => true,
            'message' => 'Logo actualizado correctamente',
            'logo_url' => asset('storage/' . $path)
        ]);
    }

    /**
     * Eliminar el logo de la empresa
     */
    public function deleteLogo()
    {
        $empresa = Empresa::first();
        
        if ($empresa && $empresa->logo) {
            if (Storage::disk('public')->exists($empresa->logo)) {
                Storage::disk('public')->delete($empresa->logo);
            }
            $empresa->logo = null;
            $empresa->save();
            
            // Limpiar caché
            Cache::forget('empresa_config');

            return response()->json([
                'success' => true,
                'message' => 'Logo eliminado correctamente'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No hay logo para eliminar'
        ], 404);
    }

    /**
     * Obtener la configuración actual (API)
     */
    public function getConfig()
    {
        $empresa = Empresa::first();
        
        return response()->json([
            'success' => true,
            'empresa' => $empresa
        ]);
    }
}
