<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiTokenController extends Controller
{
    /**
     * Muestra la lista de tokens del usuario.
     */
    public function index()
    {
        $tokens = Auth::user()->tokens;
        return view('api.tokens', compact('tokens'));
    }

    /**
     * Genera un nuevo token.
     */
    public function store(Request $request)
    {
        $request->validate([
            'token_name' => 'required|string|max:255',
        ]);

        $token = Auth::user()->createToken($request->token_name);

        // Pasamos el plainTextToken a la sesión para mostrarlo una sola vez
        return back()->with('new_token', $token->plainTextToken);
    }

    /**
     * Revoca un token.
     */
    public function destroy($id)
    {
        Auth::user()->tokens()->where('id', $id)->delete();
        return back()->with('success', 'Token revocado correctamente.');
    }
}
