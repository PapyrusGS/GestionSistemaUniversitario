<?php

use App\Http\Controllers\Api\NotaController;
use App\Http\Controllers\Api\RendimientoController;
use App\Http\Controllers\Api\ReporteNotasController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:Docente'])->group(function () {
    Route::get('/docente/cursos',           [NotaController::class, 'cursos']);
    Route::get('/docente/estudiantes',      [NotaController::class, 'estudiantes']);
    Route::get('/docente/notas',            [NotaController::class, 'notas']);
    Route::post('/docente/notas',           [NotaController::class, 'store']);
    Route::put('/docente/notas/{id}',       [NotaController::class, 'update']);
    Route::get('/docente/rendimiento/{idCursoMateria}', [RendimientoController::class, 'obtenerRendimiento']);
    Route::get('/docente/reportes/notas/{idCursoMateria}', [ReporteNotasController::class, 'obtenerReporteJSON']);
    Route::get('/docente/reportes/notas/{idCursoMateria}/pdf', [ReporteNotasController::class, 'exportarPDF']);
    Route::get('/docente/reportes/notas/{idCursoMateria}/excel', [ReporteNotasController::class, 'exportarExcel']);
});


