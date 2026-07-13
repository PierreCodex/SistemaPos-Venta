<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenController extends Controller
{
    /**
     * Muestra los tokens Sanctum del usuario autenticado y las habilidades disponibles.
     */
    public function index()
    {
        $user = Auth::user();

        // Habilidades disponibles = permisos de API que el usuario posee (directos o por rol)
        $habilidadesDisponibles = $user->getAllPermissions()
            ->pluck('name')
            ->filter(function ($permiso) {
                return str_starts_with($permiso, 'api.');
            })
            ->values();

        $tokens = $user->tokens()->latest()->get();

        return view('apis.tokens.index', compact('tokens', 'habilidadesDisponibles'));
    }

    /**
     * Crea un nuevo token Sanctum para el usuario autenticado.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'habilidades' => 'nullable|array',
            'habilidades.*' => 'string',
        ]);

        $user = Auth::user();

        // Solo se permiten habilidades que el usuario realmente tenga (directos o por rol)
        $habilidadesPermitidas = $user->getAllPermissions()
            ->pluck('name')
            ->filter(function ($permiso) {
                return str_starts_with($permiso, 'api.');
            });

        $habilidades = collect($request->get('habilidades', []))
            ->intersect($habilidadesPermitidas)
            ->values()
            ->all();

        // Si no se eligió ninguna, el token queda con ability comodín ['*'] = acceso a
        // todas las APIs del usuario. OJO: Sanctum interpreta un array VACÍO como "sin
        // ninguna ability" (tokenCan() devuelve false para todo), lo que rompía los
        // endpoints protegidos por el middleware api-ability (daban 403). Con ['*'] el
        // token funciona y los middleware can: siguen filtrando por permisos del usuario.
        if (empty($habilidades)) {
            $habilidades = ['*'];
        }

        $token = $user->createToken($request->get('name'), $habilidades);

        return redirect()
            ->route('apis.tokens.index')
            ->with('tokenNuevo', $token->plainTextToken)
            ->with('nombreToken', $request->get('name'))
            ->with('success', 'Token creado correctamente. Guárdalo ahora, no se volverá a mostrar.');
    }

    /**
     * Revoca (elimina) un token Sanctum del usuario autenticado.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $token = $user->tokens()->findOrFail($id);
        $token->delete();

        return redirect()
            ->route('apis.tokens.index')
            ->with('success', 'Token revocado correctamente.');
    }
}
