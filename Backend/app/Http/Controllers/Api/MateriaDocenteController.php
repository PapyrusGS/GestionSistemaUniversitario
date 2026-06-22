<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MateriaDocenteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Exception;

class MateriaDocenteController extends Controller
{
    protected MateriaDocenteService $service;

    public function __construct(MateriaDocenteService $service)
    {
        $this->service = $service;
    }

    /**
     * HU-DOC-02: Obtener las materias asignadas al docente autenticado
     */
    public function getCursosAsignados(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Sesión no válida o usuario ausente en el contexto.'
                ], 401);
            }

            // Captura de la clave primaria del usuario autenticado
            $idDocente = $user->idUsuario ?? $user->id ?? $user->id_usuario;

            if (is_null($idDocente)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'El identificador del usuario no pudo ser determinado en el payload del token.'
                ], 422);
            }

            // Invocación al servicio con casteo estricto a entero
            $cursos = $this->service->obtenerCursosPorDocente((int)$idDocente);

            return response()->json([
                'status'  => 'success',
                'message' => 'Cursos recuperados correctamente.',
                'data'    => $cursos
            ], 200);

        } catch (QueryException $qe) {
            // EXPOSICIÓN DIRECTA: Esto nos devolverá el error real de MySQL a la consola de Vue
            return response()->json([
                'status'  => 'error',
                'message' => 'Error explícito en la base de datos: ' . $qe->getMessage(),
                'sql'     => $qe->getSql(),
                'bindings'=> $qe->getBindings()
            ], 500);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Falla de procesamiento interno en el servidor.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * HU-DOC-03: Obtener la lista de alumnos inscritos en un curso-materia determinado
     */
    public function getEstudiantesInscritos(Request $request, $idCursoMateria): JsonResponse
    {
        try {
            $user = $request->user();
            
            $idDocente = $user->idUsuario ?? $user->id ?? $user->id_usuario;

            if (is_null($idDocente)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Autorización denegada: Parámetro del docente ausente.'
                ], 422);
            }

            $estudiantes = $this->service->obtenerEstudiantesInscritos((int)$idCursoMateria, (int)$idDocente);

            return response()->json([
                'status'  => 'success',
                'message' => 'Listado de estudiantes recuperado.',
                'data'    => $estudiantes
            ], 200);

        } catch (QueryException $qe) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error en ejecución de base de datos al buscar estudiantes: ' . $qe->getMessage()
            ], 500);
            
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No se pudo procesar la consulta de alumnos.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}