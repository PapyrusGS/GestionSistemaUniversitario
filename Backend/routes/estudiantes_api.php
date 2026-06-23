<?php

use App\Http\Controllers\Api\EstudianteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:Estudiante'])->prefix('estudiante')->group(function () {
    Route::get('/dashboard', [EstudianteController::class, 'dashboard']);
    Route::get('/materias-disponibles', [EstudianteController::class, 'materiasDisponibles']);
    Route::post('/inscribir', [EstudianteController::class, 'inscribir']);
    Route::get('/inscripciones', [EstudianteController::class, 'inscripciones']);
    Route::get('/notas', [EstudianteController::class, 'notas']);
    Route::get('/historial', [EstudianteController::class, 'historial']);
    Route::get('/malla', [EstudianteController::class, 'malla']);
    Route::post('/reporte', [EstudianteController::class, 'reporte']);
});
