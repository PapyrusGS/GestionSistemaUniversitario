<?php

use App\Http\Controllers\Api\CarreraController;
use App\Http\Controllers\Api\CursoController;
use App\Http\Controllers\Api\CursoFisicoController;
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
    Route::patch('/carreras/{carrera}/enable', [CarreraController::class, 'enable']);

    // ── Materias ──────────────────────────────────────────────────────────────
    Route::get('/materias',              [MateriaController::class, 'index']);
    Route::get('/materias/form-data',    [MateriaController::class, 'formData']);
    Route::post('/materias',             [MateriaController::class, 'store']);
    Route::put('/materias/{materia}',    [MateriaController::class, 'update']);
    Route::delete('/materias/{materia}', [MateriaController::class, 'destroy']);
    Route::patch('/materias/{materia}/enable', [MateriaController::class, 'enable']);

    // ── Cursos ────────────────────────────────────────────────────────────────
    Route::get('/cursos',                                      [CursoController::class, 'index']);
    Route::get('/cursos/form-data',                            [CursoController::class, 'formData']);
    Route::post('/cursos',                                     [CursoController::class, 'store']);
    Route::put('/cursos/{cursoMateria}/asignar-docente',       [CursoController::class, 'asignarDocente']);
    Route::get('/cursos/{cursoMateria}/inscripciones',         [CursoController::class, 'inscripciones']);
    Route::delete('/cursos/{idCursoMateria}',                  [CursoController::class, 'destroy']);
    Route::patch('/cursos/{idCursoMateria}/enable',            [CursoController::class, 'enable']);

    // ── Cursos Físicos (Aulas) ────────────────────────────────────────────────
    Route::get('/cursos-fisicos',                              [CursoFisicoController::class, 'index']);
    Route::post('/cursos-fisicos',                             [CursoFisicoController::class, 'store']);
    Route::put('/cursos-fisicos/{idCurso}',                    [CursoFisicoController::class, 'update']);
    Route::delete('/cursos-fisicos/{idCurso}',                 [CursoFisicoController::class, 'destroy']);
    Route::patch('/cursos-fisicos/{idCurso}/enable',           [CursoFisicoController::class, 'enable']);

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

    // 5. Horario de Docente
    Route::get('/reportes/horario-docente',          [ReporteController::class, 'reporteHorarioDocente']);
    Route::get('/reportes/horario-docente/exportar', [ReporteController::class, 'exportarHorarioDocente']);

    // 6. Estudiantes por Carrera
    Route::get('/reportes/estudiantes-carrera',          [ReporteController::class, 'reporteEstudiantesCarrera']);
    Route::get('/reportes/estudiantes-carrera/exportar', [ReporteController::class, 'exportarEstudiantesCarrera']);

    // 7. Carga Docente
    Route::get('/reportes/carga-docente',          [ReporteController::class, 'reporteCargaDocente']);
    Route::get('/reportes/carga-docente/exportar', [ReporteController::class, 'exportarCargaDocente']);
});
