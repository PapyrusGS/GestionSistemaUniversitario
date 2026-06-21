<?php

use App\Http\Controllers\Api\CarreraController;
use App\Http\Controllers\Api\CursoController;
use App\Http\Controllers\Api\MateriaController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:Administrador'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/roles', [UserController::class, 'roles']);

    Route::get('/carreras', [CarreraController::class, 'index']);
    Route::post('/carreras', [CarreraController::class, 'store']);
    Route::put('/carreras/{carrera}', [CarreraController::class, 'update']);
    Route::delete('/carreras/{carrera}', [CarreraController::class, 'destroy']);

    Route::get('/materias', [MateriaController::class, 'index']);
    Route::get('/materias/form-data', [MateriaController::class, 'formData']);
    Route::post('/materias', [MateriaController::class, 'store']);
    Route::put('/materias/{materia}', [MateriaController::class, 'update']);
    Route::delete('/materias/{materia}', [MateriaController::class, 'destroy']);

    Route::get('/cursos', [CursoController::class, 'index']);
    Route::get('/cursos/form-data', [CursoController::class, 'formData']);
    Route::post('/cursos', [CursoController::class, 'store']);
});
