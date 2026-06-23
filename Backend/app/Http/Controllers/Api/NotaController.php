<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditarNotaRequest;
use App\Http\Requests\RegistrarNotaRequest;
use App\Support\ApiResponse;
use App\Services\NotaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;

class NotaController extends Controller
{
    public function __construct(
        private readonly NotaService $notaService,
    ) {
    }

    public function store(RegistrarNotaRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            // Utilizar el primary key exacto del modelo: idUsuario
            $docenteId = $user->idUsuario;

            $data = array_merge($request->validated(), [
                'docente_id' => $docenteId,
            ]);

            $nota = $this->notaService->store($data);

            return ApiResponse::success(
                ['nota' => $nota],
                'Nota registrada correctamente.',
                201
            );
        } catch (QueryException $e) {
            // Extraer el mensaje específico lanzado por SIGNAL SQLSTATE en MySQL
            $errorInfo = $e->errorInfo;
            $message = isset($errorInfo[2]) ? $errorInfo[2] : $e->getMessage();
            return ApiResponse::error($message, null, 400);
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), null, 400);
        }
    }

    public function update(EditarNotaRequest $request, int $notaId): JsonResponse
    {
        try {
            $docenteId = $request->user()->idUsuario;

            $nota = $this->notaService->update($docenteId, $notaId, $request->validated());

            return ApiResponse::success(
                ['nota' => $nota],
                'Calificación actualizada correctamente.'
            );
        } catch (QueryException $e) {
            $errorInfo = $e->errorInfo;
            $message = isset($errorInfo[2]) ? $errorInfo[2] : $e->getMessage();
            return ApiResponse::error($message, null, 400);
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), null, 400);
        }
    }

    public function cursos(\Illuminate\Http\Request $request): JsonResponse
    {
        try {
            // Obtenemos el usuario autenticado directamente del token
            $user = $request->user();
            $idUsuario = $user->idUsuario;

            // Llamamos directamente al procedimiento pasándole el idUsuario
            $cursos = \Illuminate\Support\Facades\DB::select('CALL sp_docente_cursos_listar(?)', [$idUsuario]);

            // Forzamos compatibilidad absoluta de atributos para el componente Vue
            foreach ($cursos as $curso) {
                $valorCapacidad = $curso->max_inscritos ?? 0; 

                // Inyectamos todas las variantes de nombres posibles para asegurar que Vue lo pinte
                $curso->cupo          = $valorCapacidad;
                $curso->maxInscritos  = $valorCapacidad; 
                $curso->max_inscritos = $valorCapacidad;
                $curso->cupo_maximo   = $valorCapacidad;
                $curso->capacidad     = $valorCapacidad;
                
                $curso->alumnos       = $curso->alumnos_count ?? 0;
            }

            return ApiResponse::success($cursos, 'Cursos cargados correctamente.');
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), null, 400);
        }
    }

    public function estudiantes(\Illuminate\Http\Request $request): JsonResponse
    {
        try {
            $idCursoMateria = $request->query('idCursoMateria');
            if (!$idCursoMateria) {
                return ApiResponse::error('El parámetro idCursoMateria es requerido.', null, 400);
            }
            
            // CORRECCIÓN: Llamamos exactamente al procedimiento que creó tu compañero
            $estudiantes = \Illuminate\Support\Facades\DB::select('CALL sp_curso_estudiantes_listar(?)', [$idCursoMateria]);
            
            return ApiResponse::success($estudiantes, 'Estudiantes cargados correctamente.');
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), null, 400);
        }
    }

    public function notas(\Illuminate\Http\Request $request): JsonResponse
    {
        try {
            $idCursoMateria = $request->query('idCursoMateria');
            if (!$idCursoMateria) {
                return ApiResponse::error('El parámetro idCursoMateria es requerido.', null, 400);
            }
            $notas = \Illuminate\Support\Facades\DB::select('CALL sp_docente_notas(?)', [$idCursoMateria]);
            return ApiResponse::success($notas, 'Notas cargadas correctamente.');
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), null, 400);
        }
    }
}