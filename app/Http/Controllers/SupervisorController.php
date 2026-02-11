<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SupervisorController extends Controller
{
    /**
     * Verifica un PIN de supervisor para autorizar una acción crítica.
     */
    public function verificarPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string',
            'modulo' => 'required|string',
            'accion' => 'required|string',
            'descripcion' => 'required|string'
        ]);

        // Buscamos usuarios que tengan el rol super-admin o administrador y que tengan un PIN seteado
        $supervisores = User::role(['super-admin', 'administrador'])
            ->whereNotNull('pin')
            ->get();

        foreach ($supervisores as $supervisor) {
            if (Hash::check($request->pin, $supervisor->pin)) {
                
                // Registrar en el Log de Auditoría
                AuditService::log(
                    $request->modulo,
                    $request->accion . '_autorizada',
                    "Acción autorizada por supervisor: " . $supervisor->name . ". " . $request->descripcion,
                    ['supervisor_id' => $supervisor->id],
                    'info'
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Autorización concedida',
                    'supervisor' => $supervisor->name
                ]);
            }
        }

        // Si falló, registrar intento fallido
        AuditService::log(
            $request->modulo,
            $request->accion . '_intento_fallido',
            "Intento de autorización fallido para: " . $request->descripcion,
            ['pin_ingresado' => '****'],
            'warning'
        );

        return response()->json([
            'success' => false,
            'message' => 'PIN de supervisor incorrecto o usuario no autorizado.'
        ], 403);
    }
}
