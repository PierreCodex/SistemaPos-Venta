<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureApiAbility
{
    /**
     * Asegura que el token Sanctum en uso tenga al menos una de las abilities requeridas.
     *
     * Reglas:
     * - Si el token no tiene abilities específicas (['*']), permite todo (comportamiento por defecto de Sanctum).
     * - Si el token tiene abilities restringidas, debe coincidir con alguna de las abilities indicadas.
     *
     * Este middleware complementa al middleware 'can:', que verifica los permisos del usuario.
     */
    public function handle(Request $request, Closure $next, ...$abilities)
    {
        $user = $request->user();

        if (!$user || !$user->currentAccessToken()) {
            abort(401, 'No autenticado con token Sanctum.');
        }

        foreach ($abilities as $ability) {
            if ($user->tokenCan($ability)) {
                return $next($request);
            }
        }

        abort(403, 'El token no tiene la habilidad requerida para este endpoint.');
    }
}
