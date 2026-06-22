<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MateriaDocenteController;

/*
|--------------------------------------------------------------------------
| Rutas del Módulo de Docentes
|--------------------------------------------------------------------------
|
| Este archivo es importado automáticamente por api.php. Aquí se definen
| todos los endpoints específicos para la gestión académica del profesor,
| protegidos bajo la autenticación por tokens de Laravel Sanctum.
|
*/

Route::middleware('auth:sanctum')->group(function () {
    
    /**
     * HU-DOC-02: Obtener las materias y horarios asignados al docente autenticado
     * URL de consumo: GET http://localhost:8000/api/docente/cursos
     */
    Route::get('/docente/cursos', [MateriaDocenteController::class, 'getCursosAsignados']);

    /**
     * HU-DOC-03: Obtener la lista de alumnos inscritos en una materia/curso determinado
     * URL de consumo: GET http://localhost:8000/api/cursos-materias/{idCursoMateria}/estudiantes
     */
    Route::get('/cursos-materias/{idCursoMateria}/estudiantes', [MateriaDocenteController::class, 'getEstudiantesInscritos']);

});