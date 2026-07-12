<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\Api\ProductoApiController;
use App\Http\Controllers\Api\VigilanteApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rutas protegidas (Requieren Token + permisos de API)
Route::middleware('auth:sanctum')->group(function () {

    // Ejemplo: obtener perfil del usuario
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Productos API
    Route::middleware(['can:api.productos.ver', 'api-ability:api.productos.ver'])->group(function () {
        Route::get('/productos', [ProductoApiController::class, 'index']);
        Route::get('/productos/{id}', [ProductoApiController::class, 'show']);
    });

    // VigilanteIA (solo lectura)
    Route::get('/vigilante/ventas', [VigilanteApiController::class, 'ventas'])
        ->middleware(['can:api.vigilante.ventas', 'api-ability:api.vigilante.ventas']);
    Route::get('/vigilante/stock', [VigilanteApiController::class, 'stock'])
        ->middleware(['can:api.vigilante.stock', 'api-ability:api.vigilante.stock']);

});

