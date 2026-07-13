<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoriaGlobalController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\VentaCreditoController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\ApiDocsController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\AjusteInventarioController;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\SupervisorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::post('/supervisor/verificar-pin', [SupervisorController::class, 'verificarPin'])->name('supervisor.verificar-pin');
});

Route::get('/repair-admin', function () {
    try {
        Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder', '--force' => true]);
        return "✅ Administrador (admin@codex.com) y Permisos reparados con éxito.";
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});

Route::get('/fix-permissions', function () {
    try {
        // 1. Usar el rol 'Admin' que ya tienes en tu base de datos (según tu imagen)
        $role = \Spatie\Permission\Models\Role::where('name', 'Admin')->first() 
                ?? \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        
        // 2. Ejecutar el seeder para crear todos los permisos faltantes
        Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder', '--force' => true]);

        // 3. Darle TODOS los permisos al rol 'Admin'
        $allPermissions = \Spatie\Permission\Models\Permission::all();
        $role->givePermissionTo($allPermissions);

        // 4. Asignar el rol al usuario
        $user = \App\Models\User::where('email', 'admin@themesbrand.com')->first() 
                ?? \App\Models\User::where('email', 'admin@codex.com')->first();
        
        if ($user) {
            $user->assignRole($role);
            return "✅ ¡LISTO! El rol '" . $role->name . "' ahora tiene los " . count($allPermissions) . " permisos del sistema. <br>Usuario: " . $user->email . " actualizado. <br><b>Ya puedes entrar y verás todo el menú.</b>";
        }
        
        return "❌ Error: No se encontró al usuario admin@themesbrand.com";
    } catch (\Exception $e) {
        return "❌ Error técnico: " . $e->getMessage();
    }
});

Auth::routes();
//Language Translation
Route::get('index/{locale}', [HomeController::class, 'lang']);

Route::get('/', [HomeController::class, 'root'])->name('root');

//Update User Details
Route::post('/update-profile/{id}', [HomeController::class, 'updateProfile'])->name('updateProfile');
Route::post('/update-password/{id}', [HomeController::class, 'updatePassword'])->name('updatePassword');

// =====================================================
// 🎯 RUTAS DEL SISTEMA POS
// =====================================================
// Estas rutas usan middleware 'auth' para requerir login

Route::middleware('auth')->group(function () {

    // NOTA: la documentación de API (Swagger) y la gestión de tokens viven más abajo
    // en el grupo protegido por 'permission:apis.ver' / 'permission:apis.tokens.gestionar'
    // (rutas apis.* ). Las rutas antiguas /api-docs y /api-tokens se eliminaron porque
    // estaban SOLO tras 'auth' (sin permiso) — cualquier usuario logueado podía crear
    // tokens. Usar siempre las rutas apis.* del menú.

// =========================================================================
    // CATEGORÍAS GLOBALES - Con permisos granulares
    // =========================================================================
    Route::middleware('permission:categorias.ver')->group(function () {
        Route::get('categorias-globales', [CategoriaGlobalController::class, 'index'])->name('categorias-globales.index');
        Route::get('categorias-globales/{categoriaGlobal}', [CategoriaGlobalController::class, 'show'])->name('categorias-globales.show');
    });
    Route::middleware('permission:categorias.crear')->group(function () {
        Route::get('categorias-globales/create', [CategoriaGlobalController::class, 'create'])->name('categorias-globales.create');
        Route::post('categorias-globales', [CategoriaGlobalController::class, 'store'])->name('categorias-globales.store');
    });
    Route::middleware('permission:categorias.editar')->group(function () {
        Route::get('categorias-globales/{categoriaGlobal}/edit', [CategoriaGlobalController::class, 'edit'])->name('categorias-globales.edit');
        Route::put('categorias-globales/{categoriaGlobal}', [CategoriaGlobalController::class, 'update'])->name('categorias-globales.update');
        Route::patch('categorias-globales/{categoriaGlobal}', [CategoriaGlobalController::class, 'update']);
        Route::patch('categorias-globales/{id}/toggle-estado', [CategoriaGlobalController::class, 'toggleEstado'])
            ->name('categorias-globales.toggle-estado');
    });
    Route::middleware('permission:categorias.eliminar')->group(function () {
        Route::delete('categorias-globales/{categoriaGlobal}', [CategoriaGlobalController::class, 'destroy'])->name('categorias-globales.destroy');
    });
    
    // =========================================================================
    // CATEGORÍAS (Subcategorías) - Con permisos granulares
    // =========================================================================
    Route::middleware('permission:categorias.ver')->group(function () {
        Route::get('categorias', [CategoriaController::class, 'index'])->name('categorias.index');
        Route::get('categorias/{categoria}', [CategoriaController::class, 'show'])->name('categorias.show');
    });
    Route::middleware('permission:categorias.crear')->group(function () {
        Route::get('categorias/create', [CategoriaController::class, 'create'])->name('categorias.create');
        Route::post('categorias', [CategoriaController::class, 'store'])->name('categorias.store');
    });
    Route::middleware('permission:categorias.editar')->group(function () {
        Route::get('categorias/{categoria}/edit', [CategoriaController::class, 'edit'])->name('categorias.edit');
        Route::put('categorias/{categoria}', [CategoriaController::class, 'update'])->name('categorias.update');
        Route::patch('categorias/{categoria}', [CategoriaController::class, 'update']);
        Route::patch('categorias/{id}/toggle-estado', [CategoriaController::class, 'toggleEstado'])
            ->name('categorias.toggle-estado');
    });
    Route::middleware('permission:categorias.eliminar')->group(function () {
        Route::delete('categorias/{categoria}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
    });

    // =========================================================================
    // MARCAS - Con permisos granulares
    // =========================================================================
    Route::middleware('permission:marcas.ver')->group(function () {
        Route::get('marcas', [MarcaController::class, 'index'])->name('marcas.index');
        Route::get('marcas/{marca}', [MarcaController::class, 'show'])->name('marcas.show');
    });
    Route::middleware('permission:marcas.crear')->group(function () {
        Route::get('marcas/create', [MarcaController::class, 'create'])->name('marcas.create');
        Route::post('marcas', [MarcaController::class, 'store'])->name('marcas.store');
    });
    Route::middleware('permission:marcas.editar')->group(function () {
        Route::get('marcas/{marca}/edit', [MarcaController::class, 'edit'])->name('marcas.edit');
        Route::put('marcas/{marca}', [MarcaController::class, 'update'])->name('marcas.update');
        Route::patch('marcas/{marca}', [MarcaController::class, 'update']);
        Route::patch('marcas/{id}/toggle-estado', [MarcaController::class, 'toggleEstado'])
            ->name('marcas.toggle-estado');
    });
    Route::middleware('permission:marcas.eliminar')->group(function () {
        Route::delete('marcas/{marca}', [MarcaController::class, 'destroy'])->name('marcas.destroy');
    });

    // =========================================================================
    // UNIDADES DE MEDIDA - Con permisos granulares
    // =========================================================================
    Route::middleware('permission:unidades.ver')->group(function () {
        Route::get('unidades', [UnidadController::class, 'index'])->name('unidades.index');
        Route::get('unidades/{unidad}', [UnidadController::class, 'show'])->name('unidades.show');
    });
    Route::middleware('permission:unidades.crear')->group(function () {
        Route::get('unidades/create', [UnidadController::class, 'create'])->name('unidades.create');
        Route::post('unidades', [UnidadController::class, 'store'])->name('unidades.store');
    });
    Route::middleware('permission:unidades.editar')->group(function () {
        Route::get('unidades/{unidad}/edit', [UnidadController::class, 'edit'])->name('unidades.edit');
        Route::put('unidades/{unidad}', [UnidadController::class, 'update'])->name('unidades.update');
        Route::patch('unidades/{unidad}', [UnidadController::class, 'update']);
        Route::patch('unidades/{id}/toggle-estado', [UnidadController::class, 'toggleEstado'])
            ->name('unidades.toggle-estado');
    });
    Route::middleware('permission:unidades.eliminar')->group(function () {
        Route::delete('unidades/{unidad}', [UnidadController::class, 'destroy'])->name('unidades.destroy');
    });

    // =========================================================================
    // PROVEEDORES - Con permisos granulares
    // =========================================================================
    Route::middleware('permission:proveedores.ver')->group(function () {
        Route::get('proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
        Route::get('proveedores/{proveedor}', [ProveedorController::class, 'show'])->name('proveedores.show');
    });
    Route::middleware('permission:proveedores.crear')->group(function () {
        Route::get('proveedores/create', [ProveedorController::class, 'create'])->name('proveedores.create');
        Route::post('proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
    });
    Route::middleware('permission:proveedores.editar')->group(function () {
        Route::get('proveedores/{proveedor}/edit', [ProveedorController::class, 'edit'])->name('proveedores.edit');
        Route::put('proveedores/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update');
        Route::patch('proveedores/{proveedor}', [ProveedorController::class, 'update']);
        Route::patch('proveedores/{id}/toggle-estado', [ProveedorController::class, 'toggleEstado'])
            ->name('proveedores.toggle-estado');
    });
    Route::middleware('permission:proveedores.eliminar')->group(function () {
        Route::delete('proveedores/{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
    });

    // =========================================================================
    // PRODUCTOS - Con permisos granulares
    // =========================================================================
    Route::middleware('permission:productos.ver')->group(function () {
        Route::get('productos', [ProductoController::class, 'index'])->name('productos.index');
        Route::get('productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');
    });
    Route::middleware('permission:productos.crear')->group(function () {
        Route::get('productos/create', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('productos', [ProductoController::class, 'store'])->name('productos.store');
    });
    Route::middleware('permission:productos.editar')->group(function () {
        Route::get('productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::patch('productos/{producto}', [ProductoController::class, 'update']);
        Route::patch('productos/{id}/toggle-estado', [ProductoController::class, 'toggleEstado'])
            ->name('productos.toggle-estado');
    });
    Route::middleware('permission:productos.eliminar')->group(function () {
        Route::delete('productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    });

    // =========================================================================
    // VENTAS (POS) - Con permisos granulares
    // =========================================================================
    // IMPORTANTE: Las rutas específicas deben ir ANTES de las rutas con parámetros
    Route::middleware('permission:ventas.crear')->group(function () {
        Route::get('ventas/create', [VentaController::class, 'create'])->name('ventas.create');
        Route::post('ventas', [VentaController::class, 'store'])->name('ventas.store');
    });
    Route::middleware('permission:ventas.anular')->group(function () {
        Route::delete('ventas/{venta}', [VentaController::class, 'destroy'])->name('ventas.destroy');
    });
    Route::middleware('permission:ventas.ver')->group(function () {
        Route::get('ventas', [VentaController::class, 'index'])->name('ventas.index');
        Route::get('ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');
        
        // API endpoints para búsquedas (solo requieren ver)
        Route::prefix('ventas')->name('ventas.')->group(function () {
            Route::get('api/buscar-producto', [VentaController::class, 'buscarProducto'])->name('buscar-producto');
            Route::get('api/buscar-cliente', [VentaController::class, 'buscarCliente'])->name('buscar-cliente');
            Route::get('api/buscar-codigo-barras', [VentaController::class, 'buscarPorCodigoBarras'])->name('buscar-codigo-barras');
            Route::get('api/productos-categoria/{categoria}', [VentaController::class, 'productosPorCategoria'])->name('productos-categoria');
            Route::post('api/filtrar-fechas', [VentaController::class, 'filtrarPorFechas'])->name('filtrar-fechas');
            Route::get('{id}/pdf/{formato}', [VentaController::class, 'descargarPdf'])->name('descargar-pdf');
        });
    });

    // =========================================================================
    // VENTAS A CRÉDITO (Cuentas por cobrar) - Con permisos granulares
    // =========================================================================
    Route::middleware('permission:creditos.ver')->group(function () {
        Route::prefix('ventas-credito')->name('ventas-credito.')->group(function () {
            Route::get('/', [VentaCreditoController::class, 'index'])->name('index');
            Route::get('historial-general', [VentaCreditoController::class, 'historialGeneral'])->name('historial-general');
            Route::get('{id}', [VentaCreditoController::class, 'show'])->name('show');
            Route::get('{id}/historial', [VentaCreditoController::class, 'historialPagos'])->name('historial-pagos');
        });
    });
    Route::middleware('permission:creditos.cobrar')->group(function () {
        Route::post('ventas-credito/{id}/pagar', [VentaCreditoController::class, 'registrarPago'])->name('ventas-credito.registrar-pago');
    });

    // =========================================================================
    // ROLES Y PERMISOS - Con permisos granulares
    // =========================================================================
    Route::middleware('permission:roles.ver')->group(function () {
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RolePermissionController::class, 'index'])->name('index');
            Route::get('usuarios', [RolePermissionController::class, 'usuarios'])->name('usuarios');
            Route::get('api/list', [RolePermissionController::class, 'getRoles'])->name('api.list');
            Route::get('api/{id}/permissions', [RolePermissionController::class, 'getRolePermissions'])->name('api.permissions');
            Route::get('api/permissions/list', [RolePermissionController::class, 'getPermissions'])->name('api.permissions.list');
        });
    });
    Route::middleware('permission:roles.crear')->group(function () {
        Route::post('roles/api/store', [RolePermissionController::class, 'storeRole'])->name('roles.api.store');
        Route::post('roles/api/permissions/store', [RolePermissionController::class, 'storePermission'])->name('roles.api.permissions.store');
    });
    Route::middleware('permission:roles.editar')->group(function () {
        Route::put('roles/api/{id}', [RolePermissionController::class, 'updateRole'])->name('roles.api.update');
    });
    Route::middleware('permission:roles.eliminar')->group(function () {
        Route::delete('roles/api/{id}', [RolePermissionController::class, 'destroyRole'])->name('roles.api.destroy');
    });
    Route::middleware('permission:roles.asignar')->group(function () {
        Route::post('roles/api/usuarios/{userId}/assign', [RolePermissionController::class, 'assignRole'])->name('roles.api.usuarios.assign');
    });

    // =========================================================================
    // DOCUMENTACIÓN DE APIs (Swagger UI)
    // =========================================================================
    Route::middleware('permission:apis.ver')->group(function () {
        Route::get('apis', [ApiDocsController::class, 'index'])->name('apis.index');
        Route::get('apis/swagger.json', [ApiDocsController::class, 'swaggerJson'])->name('apis.swagger');
    });

    Route::middleware('permission:apis.tokens.gestionar')->group(function () {
        Route::get('apis/tokens', [ApiTokenController::class, 'index'])->name('apis.tokens.index');
        Route::post('apis/tokens', [ApiTokenController::class, 'store'])->name('apis.tokens.store');
        Route::delete('apis/tokens/{id}', [ApiTokenController::class, 'destroy'])->name('apis.tokens.destroy');
    });

    // =========================================================================
    // USUARIOS - Con permisos granulares
    // =========================================================================
    Route::middleware('permission:usuarios.ver')->group(function () {
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('api/list', [UserController::class, 'getUsers'])->name('api.list');
            Route::get('api/{id}', [UserController::class, 'show'])->name('api.show');
        });
    });
    Route::middleware('permission:usuarios.crear')->group(function () {
        Route::post('usuarios/api/store', [UserController::class, 'store'])->name('usuarios.api.store');
    });
    Route::middleware('permission:usuarios.editar')->group(function () {
        Route::put('usuarios/api/{id}', [UserController::class, 'update'])->name('usuarios.api.update');
    });
    Route::middleware('permission:usuarios.eliminar')->group(function () {
        Route::delete('usuarios/api/{id}', [UserController::class, 'destroy'])->name('usuarios.api.destroy');
    });
    Route::middleware('permission:roles.asignar')->group(function () {
        Route::post('usuarios/api/{id}/roles', [UserController::class, 'assignRoles'])->name('usuarios.api.roles');
    });

    // =========================================================================
    // CONFIGURACIÓN DEL SISTEMA - Con permisos granulares
    // =========================================================================
    Route::middleware('permission:configuracion.ver')->group(function () {
        Route::get('configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::get('configuracion/api', [ConfiguracionController::class, 'getConfig'])->name('configuracion.api');
    });
    Route::middleware('permission:configuracion.editar')->group(function () {
        Route::put('configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');
        Route::post('configuracion/upload-logo', [ConfiguracionController::class, 'uploadLogo'])->name('configuracion.upload-logo');
        Route::post('configuracion/upload-cert', [ConfiguracionController::class, 'uploadCertificado'])->name('configuracion.upload-cert');
        Route::delete('configuracion/delete-logo', [ConfiguracionController::class, 'deleteLogo'])->name('configuracion.delete-logo');
    });

    // =========================================================================
    // COMPRAS (Ingreso de Mercadería) - Con permisos granulares
    // =========================================================================
    Route::middleware('permission:compras.crear')->group(function () {
        Route::get('compras/create', [CompraController::class, 'create'])->name('compras.create');
        Route::post('compras', [CompraController::class, 'store'])->name('compras.store');
    });
    Route::middleware('permission:compras.ver')->group(function () {
        Route::get('compras', [CompraController::class, 'index'])->name('compras.index');
        Route::get('compras/api/buscar-productos', [CompraController::class, 'buscarProductos'])->name('compras.buscar-productos');
        Route::get('compras/{compra}', [CompraController::class, 'show'])->name('compras.show');
    });
    Route::middleware('permission:compras.anular')->group(function () {
        Route::patch('compras/{compra}/anular', [CompraController::class, 'anular'])->name('compras.anular');
    });

    // =========================================================================
    // AJUSTES DE INVENTARIO - Con permisos granulares
    // =========================================================================
    Route::middleware('permission:inventario.ajustar')->group(function () {
        Route::prefix('inventario')->name('inventario.')->group(function () {
            Route::get('ajustes/create', [AjusteInventarioController::class, 'create'])->name('ajustes.create');
            Route::post('ajustes', [AjusteInventarioController::class, 'store'])->name('ajustes.store');
            Route::patch('ajustes/{ajuste}/aplicar', [AjusteInventarioController::class, 'aplicar'])->name('ajustes.aplicar');
            Route::patch('ajustes/{ajuste}/anular', [AjusteInventarioController::class, 'anular'])->name('ajustes.anular');
            Route::delete('ajustes/{ajuste}', [AjusteInventarioController::class, 'destroy'])->name('ajustes.destroy');
        });
    });
    Route::middleware('permission:inventario.ver')->group(function () {
        Route::prefix('inventario')->name('inventario.')->group(function () {
            // Ajustes - rutas API y estáticas primero
            Route::get('ajustes', [AjusteInventarioController::class, 'index'])->name('ajustes.index');
            Route::get('ajustes/api/buscar-productos', [AjusteInventarioController::class, 'buscarProductos'])->name('ajustes.buscar-productos');
            // Rutas con parámetros al final
            Route::get('ajustes/{ajuste}', [AjusteInventarioController::class, 'show'])->name('ajustes.show');
        });
    });

    // =========================================================================
    // KARDEX (Historial de Movimientos) - Con permisos granulares
    // =========================================================================
    Route::middleware('permission:inventario.ver')->group(function () {
        Route::prefix('inventario')->name('inventario.')->group(function () {
            Route::get('kardex', [KardexController::class, 'index'])->name('kardex.index');
            Route::get('kardex/producto/{producto}', [KardexController::class, 'porProducto'])->name('kardex.producto');
            Route::get('kardex/api/movimientos', [KardexController::class, 'obtenerMovimientos'])->name('kardex.api.movimientos');
            Route::get('kardex/api/estadisticas', [KardexController::class, 'estadisticas'])->name('kardex.api.estadisticas');
            Route::get('kardex/api/resumen-tipo', [KardexController::class, 'resumenPorTipo'])->name('kardex.api.resumen-tipo');
            Route::get('kardex/api/exportar/{producto}', [KardexController::class, 'exportar'])->name('kardex.api.exportar');
            Route::get('kardex/api/buscar-productos', [KardexController::class, 'buscarProductos'])->name('kardex.api.buscar-productos');
        });
    });

    // =========================================================================
    // CAJA (Apertura, Cierre, Movimientos) - Con permisos granulares
    // =========================================================================
    Route::prefix('caja')->name('caja.')->group(function () {
        // Rutas de visualización
        Route::middleware('permission:caja.ver')->group(function () {
            Route::get('/', [CajaController::class, 'index'])->name('index');
            Route::get('/estado', [CajaController::class, 'estadoJson'])->name('estado.json');
            Route::get('/verificar', [CajaController::class, 'verificarCajaJson'])->name('verificar.json');
            Route::get('/pagos-credito/{id?}', [CajaController::class, 'pagosCredito'])->name('pagos-credito')->where('id', '[0-9]+');
            Route::get('/{id}', [CajaController::class, 'show'])->name('show')->where('id', '[0-9]+');
            Route::get('/{id}/movimientos', [CajaController::class, 'movimientos'])->name('movimientos')->where('id', '[0-9]+');
        });

        // Rutas de apertura
        Route::middleware('permission:caja.abrir')->group(function () {
            Route::get('/apertura', [CajaController::class, 'apertura'])->name('apertura');
            Route::post('/abrir', [CajaController::class, 'abrirCaja'])->name('abrir');
        });

        // Rutas de cierre
        Route::middleware('permission:caja.cerrar')->group(function () {
            Route::get('/cierre', [CajaController::class, 'cierre'])->name('cierre');
            Route::post('/cerrar', [CajaController::class, 'cerrarCaja'])->name('cerrar');
        });

        // Rutas de movimientos manuales
        Route::middleware('permission:caja.movimientos')->group(function () {
            Route::get('/movimiento', [CajaController::class, 'movimientoForm'])->name('movimiento.form');
            Route::post('/movimiento', [CajaController::class, 'storeMovimiento'])->name('movimiento.store');
        });

        // Rutas de reportes
        Route::middleware('permission:caja.reporte')->group(function () {
            Route::get('/reporte', [CajaController::class, 'reporte'])->name('reporte');
        });
    });

    // =========================================================================
    // HORARIOS - Con permisos granulares
    // =========================================================================
    Route::middleware('permission:horarios.ver_mio')->group(function () {
        Route::get('horarios/mi-horario', [HorarioController::class, 'miHorario'])->name('horarios.mio');
    });

    Route::middleware('permission:horarios.ver')->group(function () {
        Route::get('horarios', [HorarioController::class, 'index'])->name('horarios.index');
        Route::get('horarios/{id}/usuarios', [HorarioController::class, 'getUsuariosAsignados'])->name('horarios.usuarios');
    });
    Route::middleware('permission:horarios.crear')->group(function () {
        Route::post('horarios', [HorarioController::class, 'store'])->name('horarios.store');
    });
    Route::middleware('permission:horarios.editar')->group(function () {
        Route::put('horarios/{id}', [HorarioController::class, 'update'])->name('horarios.update');
        Route::patch('horarios/{id}/toggle-estado', [HorarioController::class, 'toggleEstado'])->name('horarios.toggle-estado');
    });
    Route::middleware('permission:horarios.eliminar')->group(function () {
        Route::delete('horarios/{id}', [HorarioController::class, 'destroy'])->name('horarios.destroy');
    });
    Route::middleware('permission:horarios.asignar')->group(function () {
        Route::post('horarios/asignar-usuarios', [HorarioController::class, 'asignarUsuarios'])->name('horarios.asignar-usuarios');
    });

    // =========================================================================
    // ASISTENCIAS - Con permisos granulares
    // =========================================================================
    Route::middleware('permission:asistencias.ver|asistencias.ver_mio')->group(function () {
        Route::get('asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index');
        Route::get('asistencias/usuario/{id}', [AsistenciaController::class, 'show'])->name('asistencias.show');
        Route::get('asistencias/api/calendario/{userId}', [AsistenciaController::class, 'calendarioData'])->name('asistencias.calendario-data');
        Route::get('asistencias/api/reporte', [AsistenciaController::class, 'generarNomina'])->name('asistencias.reporte');
    });
    Route::middleware('permission:asistencias.registrar')->group(function () {
        Route::post('asistencias', [AsistenciaController::class, 'store'])->name('asistencias.store');
        Route::post('asistencias/evento', [AsistenciaController::class, 'registrarEvento'])->name('asistencias.evento');
    });
});


Route::get('/ticket/{codigo}', [App\Http\Controllers\TicketController::class, 'publico'])->name('ticket.publico');

// ⚠️ Esta ruta DEBE ir al final porque captura CUALQUIER URL
Route::get('{any}', [HomeController::class, 'index'])->name('index');
