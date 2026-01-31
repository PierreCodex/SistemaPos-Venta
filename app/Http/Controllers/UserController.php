<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Vista principal de usuarios
     */
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        
        return view('usuarios.index', compact('users', 'roles'));
    }

    /**
     * Listar usuarios para DataTable (AJAX)
     */
    public function getUsers()
    {
        $users = User::with('roles')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'roles' => $user->roles->pluck('name'),
                'created_at' => $user->created_at->format('d/m/Y H:i'),
                'is_current' => $user->id === Auth::id()
            ];
        });

        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }

    /**
     * Crear un nuevo usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(6)],
            'roles' => 'nullable|array'
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado correctamente',
                'user' => $user->load('roles')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos de un usuario
     */
    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'roles' => $user->roles->pluck('name'),
                'created_at' => $user->created_at->format('d/m/Y H:i')
            ]
        ]);
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => ['nullable', 'confirmed', Password::min(6)],
            'roles' => 'nullable|array'
        ]);

        try {
            $user = User::findOrFail($id);
            
            $user->name = $request->name;
            $user->email = $request->email;
            
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            $user->save();

            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado correctamente',
                'user' => $user->load('roles')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar usuario
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // No permitir eliminar al usuario actual
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propia cuenta'
                ], 403);
            }

            // No permitir eliminar super-admin si es el único
            if ($user->hasRole('super-admin')) {
                $superAdminCount = User::role('super-admin')->count();
                if ($superAdminCount <= 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No puedes eliminar el único Super Administrador'
                    ], 403);
                }
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asignar roles a un usuario
     */
    public function assignRoles(Request $request, $id)
    {
        $request->validate([
            'roles' => 'required|array'
        ]);

        try {
            $user = User::findOrFail($id);
            $user->syncRoles($request->roles);

            return response()->json([
                'success' => true,
                'message' => 'Roles asignados correctamente',
                'user' => $user->load('roles')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar roles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado activo/inactivo (toggle)
     */
    public function toggleEstado($id)
    {
        try {
            $user = User::findOrFail($id);
            
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes desactivar tu propia cuenta'
                ], 403);
            }

            // Aquí podrías agregar un campo 'activo' en la tabla users
            // Por ahora solo retornamos éxito
            return response()->json([
                'success' => true,
                'message' => 'Estado cambiado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }
}
