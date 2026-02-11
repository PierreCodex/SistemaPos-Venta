<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('roles.index', compact('roles', 'permissions'));
    }

    public function getRoles()
    {
        $roles = Role::withCount(['users', 'permissions'])->get();
        return response()->json(['success' => true, 'roles' => $roles]);
    }

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

            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            return response()->json([
                'success' => true,
                'message' => 'Rol creado y caché actualizada',
                'role' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

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

            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            return response()->json([
                'success' => true,
                'message' => 'Rol actualizado y caché refrescada',
                'role' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroyRole($id)
    {
        try {
            $role = Role::findOrFail($id);
            if ($role->name === 'super-admin') {
                return response()->json(['success' => false, 'message' => 'Protegido'], 403);
            }
            $role->delete();
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            return response()->json(['success' => true, 'message' => 'Eliminado']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getRolePermissions($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return response()->json([
            'success' => true, 
            'role' => $role, 
            'permissions' => $role->permissions->pluck('name')
        ]);
    }

    public function getPermissions()
    {
        $permissions = Permission::all()->groupBy(function ($p) {
            return explode('.', $p->name)[0] ?? 'general';
        });
        return response()->json(['success' => true, 'permissions' => $permissions]);
    }

    public function storePermission(Request $request)
    {
        $request->validate(['name' => 'required|string']);

        try {
            $permission = Permission::firstOrCreate([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);

            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            return response()->json(['success' => true, 'permission' => $permission]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function usuarios()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        return view('roles.usuarios', compact('users', 'roles'));
    }

    public function assignRole(Request $request, $userId)
    {
        $request->validate(['roles' => 'required|array']);

        try {
            $user = User::findOrFail($userId);
            $user->syncRoles($request->roles);
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            return response()->json(['success' => true, 'user' => $user->load('roles')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}