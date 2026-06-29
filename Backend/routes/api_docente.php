<?php

use App\Http\Controllers\Api\NotaController;
use App\Http\Controllers\Api\RendimientoController;
use App\Http\Controllers\Api\ReporteNotasController;
use App\Http\Controllers\Api\ReporteDocenteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:Docente'])->group(function () {
    // HU-DOC-04 / HU-DOC-05: Registrar y editar calificaciones
    Route::get('/docente/cursos',           [NotaController::class, 'cursos']);
    Route::get('/docente/estudiantes',      [NotaController::class, 'estudiantes']);
    Route::get('/docente/notas',            [NotaController::class, 'notas']);
    Route::post('/docente/notas',           [NotaController::class, 'store']);
    Route::put('/docente/notas/{id}',       [NotaController::class, 'update']);

    // HU-DOC-06: Rendimiento
    Route::get('/docente/rendimiento/{idCursoMateria}', [RendimientoController::class, 'obtenerRendimiento']);

    // HU-DOC-07: Reporte de notas por curso
    Route::get('/docente/reportes/notas/{idCursoMateria}', [ReporteNotasController::class, 'obtenerReporteJSON']);
    Route::get('/docente/reportes/notas/{idCursoMateria}/pdf', [ReporteNotasController::class, 'exportarPDF']);
    Route::get('/docente/reportes/notas/{idCursoMateria}/excel', [ReporteNotasController::class, 'exportarExcel']);

    // HU-DOC-08 y HU-DOC-10: Reportes avanzados (semestre + estadísticas)
    Route::get('/docente/reportes/filtros', [ReporteDocenteController::class, 'filtros']);
    Route::get('/docente/reportes/semestre/{idPeriodo}', [ReporteDocenteController::class, 'reporteSemestre']);
    Route::get('/docente/reportes/semestre/{idPeriodo}/pdf', [ReporteDocenteController::class, 'exportarSemestrePDF']);
    Route::get('/docente/reportes/semestre/{idPeriodo}/excel', [ReporteDocenteController::class, 'exportarSemestreExcel']);
    Route::get('/docente/reportes/estadisticas', [ReporteDocenteController::class, 'estadisticas']);
    Route::get('/docente/reportes/estadisticas/pdf', [ReporteDocenteController::class, 'exportarEstadisticasPDF']);
    Route::get('/docente/reportes/estadisticas/excel', [ReporteDocenteController::class, 'exportarEstadisticasExcel']);
});


