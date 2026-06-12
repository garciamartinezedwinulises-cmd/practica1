<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Route;
// Importamos el espacio de nombres de la v2 con un alias para evitar colisiones
use App\Http\Controllers\Api\V2 as V2;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas de la API para tu aplicación.
|
*/

// ── VERSIÓN 1 (Estable - Conserva intacto todo tu flujo original) ───────
Route::prefix('v1')->name('v1.')->group(function () {

    // Rutas autenticadas públicas V1 [cite: 165-166]
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Rutas de consulta pública para el Catálogo V1 (Vue.js)
    Route::get('categorias', [CategoriaController::class, 'index']);
    Route::get('categorias/{categoria}/productos', [CategoriaController::class, 'productos']);
    Route::get('productos', [ProductoController::class, 'index']);
    Route::get('productos/{id}', [ProductoController::class, 'show']);

    // Grupo protegido para el panel de administración V1 [cite: 167]
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        
        // Acciones de modificación protegidas para administración V1
        Route::post('productos', [ProductoController::class, 'store']);
        Route::put('productos/{id}', [ProductoController::class, 'update']);
        Route::delete('productos/{id}', [ProductoController::class, 'destroy']);
        
        Route::post('categorias', [CategoriaController::class, 'store']);
        Route::put('categorias/{id}', [CategoriaController::class, 'update']);
        Route::delete('categorias/{id}', [CategoriaController::class, 'destroy']);

        Route::post('/pedidos', [PedidoController::class, 'store']);
        Route::get('/pedidos/{id}', [PedidoController::class, 'show']);
    });
});

// ── VERSIÓN 2 (Nuevas características - Búsqueda Full-Text) ────────────
Route::prefix('v2')->name('v2.')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        // La v2 mapea el recurso de productos al nuevo controlador independiente [cite: 179]
        Route::apiResource('productos', V2\ProductoController::class);
    });
});