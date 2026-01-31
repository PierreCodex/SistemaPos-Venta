<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Definir permisos por módulo
        $permissions = [
            // Dashboard
            'dashboard.ver',
            
            // Productos
            'productos.ver',
            'productos.crear',
            'productos.editar',
            'productos.eliminar',
            'productos.exportar',
            
            // Categorías
            'categorias.ver',
            'categorias.crear',
            'categorias.editar',
            'categorias.eliminar',
            
            // Marcas
            'marcas.ver',
            'marcas.crear',
            'marcas.editar',
            'marcas.eliminar',
            
            // Unidades
            'unidades.ver',
            'unidades.crear',
            'unidades.editar',
            'unidades.eliminar',
            
            // Proveedores
            'proveedores.ver',
            'proveedores.crear',
            'proveedores.editar',
            'proveedores.eliminar',
            
            // Ventas
            'ventas.ver',
            'ventas.crear',
            'ventas.anular',
            'ventas.exportar',
            'ventas.imprimir',
            
            // Ventas a Crédito
            'creditos.ver',
            'creditos.cobrar',
            'creditos.historial',
            'creditos.anular-pago',
            
            // Usuarios
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',
            
            // Roles y Permisos
            'roles.ver',
            'roles.crear',
            'roles.editar',
            'roles.eliminar',
            'roles.asignar',
            
            // Configuración
            'configuracion.ver',
            'configuracion.editar',
            
            // Reportes
            'reportes.ventas',
            'reportes.productos',
            'reportes.creditos',
            'reportes.caja',
        ];

        // Crear todos los permisos
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Crear rol Super Admin (tiene acceso a todo)
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // Crear rol Administrador
        $admin = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'dashboard.ver',
            'productos.ver', 'productos.crear', 'productos.editar', 'productos.exportar',
            'categorias.ver', 'categorias.crear', 'categorias.editar',
            'marcas.ver', 'marcas.crear', 'marcas.editar',
            'unidades.ver', 'unidades.crear', 'unidades.editar',
            'proveedores.ver', 'proveedores.crear', 'proveedores.editar',
            'ventas.ver', 'ventas.crear', 'ventas.exportar', 'ventas.imprimir',
            'creditos.ver', 'creditos.cobrar', 'creditos.historial',
            'usuarios.ver',
            'reportes.ventas', 'reportes.productos', 'reportes.creditos',
        ]);

        // Crear rol Vendedor/Cajero
        $vendedor = Role::firstOrCreate(['name' => 'vendedor', 'guard_name' => 'web']);
        $vendedor->givePermissionTo([
            'dashboard.ver',
            'productos.ver',
            'ventas.ver', 'ventas.crear', 'ventas.imprimir',
            'creditos.ver', 'creditos.cobrar',
        ]);

        // Crear rol Almacenero
        $almacenero = Role::firstOrCreate(['name' => 'almacenero', 'guard_name' => 'web']);
        $almacenero->givePermissionTo([
            'dashboard.ver',
            'productos.ver', 'productos.crear', 'productos.editar',
            'categorias.ver',
            'marcas.ver',
            'unidades.ver',
            'proveedores.ver',
        ]);

        // Asignar super-admin al primer usuario (si existe)
        $user = User::first();
        if ($user) {
            $user->assignRole('super-admin');
        }

        $this->command->info('✅ Roles y permisos creados correctamente');
        $this->command->info('   - ' . count($permissions) . ' permisos');
        $this->command->info('   - 4 roles (super-admin, administrador, vendedor, almacenero)');
    }
}
