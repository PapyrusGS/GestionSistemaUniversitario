<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes - Panel de Control Universitario
|--------------------------------------------------------------------------
*/

// Rutas Públicas de Autenticación (Con límite de intentos para evitar fuerza bruta)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Rutas Protegidas de Autenticación Básica
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/perfil', [ProfileController::class, 'updatePassword']);
        Route::put('/profile', [ProfileController::class, 'updateProfile']);
    });
});

/**
 * Carga de Módulos Académicos Distribuidos
 * Nota: Si tus archivos secundarios no definen internamente el middleware,
 * puedes envolverlos aquí o asegurar su declaración interna en cada uno.
 */
require __DIR__.'/api_admin.php';
require __DIR__.'/api_docente.php';
require __DIR__.'/estudiantes_api.php';