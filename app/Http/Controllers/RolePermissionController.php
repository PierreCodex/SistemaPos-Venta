<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    /**
     * Vista principal de Roles y Permisos
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        
        return view('roles.index', compact('roles', 'permissions'));
    }

    /**
     * Listar roles para DataTable (AJAX)
     */
    public function getRoles()
    {
        $roles = Role::withCount(['users', 'permissions'])->get();
        
        return response()->json([
            'success' => true,
            'roles' => $roles
        ]);
    }

    /**
     * Crear un nuevo rol
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array'
        ]);

        try {
            $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
            
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            return response()->json([
                'success' => true,
                'message' => 'Rol creado correctamente',
                'role' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el rol: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar rol y sus permisos
     */
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'permissions' => 'nullable|array'
        ]);

        try {
            $role = Role::findOrFail($id);
            $role->update(['name' => $request->name]);
            
            $role->syncPermissions($request->permissions ?? []);

            return response()->json([
                'success' => true,
                'message' => 'Rol actualizado correctamente',
                'role' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el rol: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar rol
     */
    public function destroyRole($id)
    {
        try {
            $role = Role::findOrFail($id);
            
            // No permitir eliminar rol de super-admin
            if ($role->name === 'super-admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el rol de Super Administrador'
                ], 403);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rol eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el rol: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener permisos de un rol específico
     */
    public function getRolePermissions($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'role' => $role,
            'permissions' => $role->permissions->pluck('name')
        ]);
    }

    /**
     * Listar todos los permisos agrupados
     */
    public function getPermissions()
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            // Agrupar por módulo (primera palabra del nombre)
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'general';
        });

        return response()->json([
            'success' => true,
            'permissions' => $permissions
        ]);
    }

    /**
     * Crear un nuevo permiso
     */
    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name'
        ]);

        try {
            $permission = Permission::create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permiso creado correctamente',
                'permission' => $permission
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el permiso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vista de usuarios con roles
     */
    public function usuarios()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        
        return view('roles.usuarios', compact('users', 'roles'));
    }

    /**
     * Asignar rol a usuario
     */
    public function assignRole(Request $request, $userId)
    {
        $request->validate([
            'roles' => 'required|array'
        ]);

        try {
            $user = User::findOrFail($userId);
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
}
