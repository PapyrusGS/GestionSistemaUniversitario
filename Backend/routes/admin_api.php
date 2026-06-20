<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CarreraController;
use App\Http\Controllers\Api\MateriaController;
use App\Http\Controllers\Api\UserController;


Route::middleware(['auth:sanctum', 'role:Administrador'])->group(function () {
    // Usuarios y Roles (HU-ADM-02 / HU-ADM-03)
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/roles', [UserController::class, 'roles']);

    // Carreras (HU-ADM-04)
    Route::get('/carreras', [CarreraController::class, 'index']);
    Route::post('/carreras', [CarreraController::class, 'store']);
    Route::put('/carreras/{carrera}', [CarreraController::class, 'update']);
    Route::delete('/carreras/{carrera}', [CarreraController::class, 'destroy']);

    // Materias (HU-ADM-05)
    Route::get('/materias', [MateriaController::class, 'index']);
    Route::get('/materias/form-data', [MateriaController::class, 'formData']);
    Route::post('/materias', [MateriaController::class, 'store']);
    Route::put('/materias/{materia}', [MateriaController::class, 'update']);
    Route::delete('/materias/{materia}', [MateriaController::class, 'destroy']);
});
