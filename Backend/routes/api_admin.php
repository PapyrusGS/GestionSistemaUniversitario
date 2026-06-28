<?php

use App\Http\Controllers\Api\CarreraController;
use App\Http\Controllers\Api\CursoController;
use App\Http\Controllers\Api\MateriaController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:Administrador'])->group(function () {
    // ── Usuarios ──────────────────────────────────────────────────────────────
    Route::get('/users',  [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/roles',  [UserController::class, 'roles']);

    // ── Carreras ──────────────────────────────────────────────────────────────
    Route::get('/carreras',              [CarreraController::class, 'index']);
    Route::post('/carreras',             [CarreraController::class, 'store']);
    Route::put('/carreras/{carrera}',    [CarreraController::class, 'update']);
    Route::delete('/carreras/{carrera}', [CarreraController::class, 'destroy']);

    // ── Materias ──────────────────────────────────────────────────────────────
    Route::get('/materias',              [MateriaController::class, 'index']);
    Route::get('/materias/form-data',    [MateriaController::class, 'formData']);
    Route::post('/materias',             [MateriaController::class, 'store']);
    Route::put('/materias/{materia}',    [MateriaController::class, 'update']);
    Route::delete('/materias/{materia}', [MateriaController::class, 'destroy']);

    // ── Cursos ────────────────────────────────────────────────────────────────
    Route::get('/cursos',                                      [CursoController::class, 'index']);
    Route::get('/cursos/form-data',                            [CursoController::class, 'formData']);
    Route::post('/cursos',                                     [CursoController::class, 'store']);
    Route::put('/cursos/{cursoMateria}/asignar-docente',       [CursoController::class, 'asignarDocente']);
    Route::get('/cursos/{cursoMateria}/inscripciones',         [CursoController::class, 'inscripciones']);
    Route::delete('/cursos/{idCursoMateria}',                  [CursoController::class, 'destroy']);

    // ── Reportes — filtros y reporte genérico original (conservados) ──────────
    Route::get('/reportes/filtros',  [ReporteController::class, 'filtros']);
    Route::get('/reportes/exportar', [ReporteController::class, 'exportar']);
    Route::get('/reportes',          [ReporteController::class, 'index']);

    // ── Reportes especializados — IMPORTANTE: las rutas con segmentos fijos
    //    deben declararse ANTES que cualquier ruta con parámetros dinámicos ──

    // 1. Rendimiento Académico (por período y carrera)
    Route::get('/reportes/rendimiento',          [ReporteController::class, 'reporteRendimiento']);
    Route::get('/reportes/rendimiento/exportar', [ReporteController::class, 'exportarRendimiento']);

    // 2. Kárdex de Estudiante (por CI)
    Route::get('/reportes/kardex',          [ReporteController::class, 'reporteKardex']);
    Route::get('/reportes/kardex/exportar', [ReporteController::class, 'exportarKardex']);

    // 3. Auditoría de Notas (por rango de fechas)
    Route::get('/reportes/auditoria-notas',          [ReporteController::class, 'reporteAuditoriaNotas']);
    Route::get('/reportes/auditoria-notas/exportar', [ReporteController::class, 'exportarAuditoriaNotas']);

    // 4. Ocupación de Cursos (capacidad vs inscritos, por período)
    Route::get('/reportes/ocupacion',          [ReporteController::class, 'reporteOcupacion']);
    Route::get('/reportes/ocupacion/exportar', [ReporteController::class, 'exportarOcupacion']);
});
