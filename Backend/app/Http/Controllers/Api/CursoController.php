<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AsignarDocenteRequest;
use App\Http\Requests\CursoRequest;
use App\Services\CursoService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class CursoController extends Controller
{
    public function __construct(
        private readonly CursoService $cursoService,
    ) {
    }

    public function index(): JsonResponse
    {
        return ApiResponse::success(
            ['cursos' => $this->cursoService->index()],
            'Cursos cargados correctamente.'
        );
    }

    public function formData(): JsonResponse
    {
        return ApiResponse::success(
            $this->cursoService->formData(),
            'Datos de formulario cargados correctamente.'
        );
    }

    public function store(CursoRequest $request): JsonResponse
    {
        try {
            $curso = $this->cursoService->store($request->validated());

            return ApiResponse::success(
                ['curso' => $curso],
                'Curso registrado correctamente.',
                201
            );
        } catch (\Illuminate\Database\QueryException $e) {
            // Extraer solo el MESSAGE_TEXT del SIGNAL del stored procedure (ya en español legible)
            $raw = $e->getMessage();
            // Los SP usan SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = '...'
            // El mensaje viene en el formato: "SQLSTATE[45000]: ... 1644 ... MESSAGE_TEXT"
            if (preg_match('/1644 ([^"]+)$/m', $raw, $matches)) {
                $message = trim($matches[1]);
            } elseif (isset($e->errorInfo[2])) {
                $message = $e->errorInfo[2];
            } else {
                $message = 'Ocurrió un error al registrar el curso. Por favor, verifique los datos ingresados.';
            }
            return ApiResponse::error($message, null, 422);
        } catch (\Throwable $e) {
            return ApiResponse::error(
                $e->getMessage() ?: 'Ocurrió un error inesperado al registrar el curso.',
                null,
                400
            );
        }
    }

    public function asignarDocente(AsignarDocenteRequest $request, $idCursoMateria): JsonResponse
    {
        $cursoMateria = \App\Models\CursoMateria::with('curso.horarios')->findOrFail($idCursoMateria);
        $idDocente = $request->idDocente;
        
        $horariosNuevos = $cursoMateria->curso->horarios;
        
        // Find other active courses of this teacher in the same period
        $otrosCursos = \App\Models\CursoMateria::with('curso.horarios')
            ->where('idDocente', $idDocente)
            ->where('idPeriodo', $cursoMateria->idPeriodo)
            ->where('estado', 1)
            ->where('idCursoMateria', '!=', $idCursoMateria)
            ->get();
            
        // Check conflicts
        foreach ($otrosCursos as $otroCurso) {
            if (!$otroCurso->curso) continue;
            foreach ($otroCurso->curso->horarios as $horarioOtro) {
                foreach ($horariosNuevos as $horarioNuevo) {
                    if ($horarioNuevo->diaSemana === $horarioOtro->diaSemana) {
                        // Check time overlap: (StartA < EndB) and (EndA > StartB)
                        if ($horarioNuevo->horaInicio < $horarioOtro->horaFin && $horarioNuevo->horaFin > $horarioOtro->horaInicio) {
                            return ApiResponse::error('El docente seleccionado ya tiene un curso asignado en ese mismo horario/turno.', 422);
                        }
                    }
                }
            }
        }
        
        $cursoMateria->idDocente = $idDocente;
        $cursoMateria->save();
        
        return ApiResponse::success(['curso_materia' => $cursoMateria], 'Docente asignado correctamente.');
    }

    public function inscripciones($idCursoMateria): JsonResponse
    {
        $cursoMateria = \App\Models\CursoMateria::with(['inscripciones' => function($q) {
            $q->where('estado', 1)->with('estudiante');
        }])->findOrFail($idCursoMateria);
        
        $inscripciones = $cursoMateria->inscripciones;
        $cuposOcupados = $inscripciones->count();
        $cuposDisponibles = max(0, $cursoMateria->maxInscritos - $cuposOcupados);
        
        $estudiantes = $inscripciones->map(function ($inscripcion) {
            $estudiante = $inscripcion->estudiante;
            return [
                'idInscripcion' => $inscripcion->idInscripcion,
                'fecha' => $inscripcion->fecha,
                'estudiante' => $estudiante ? [
                    'id' => $estudiante->idUsuario,
                    'nombreCompleto' => collect([
                        $estudiante->nombre1,
                        $estudiante->nombre2,
                        $estudiante->apellido1,
                        $estudiante->apellido2,
                    ])->filter()->implode(' '),
                    'ci' => $estudiante->ci,
                    'correo' => $estudiante->correo,
                ] : null
            ];
        });
        
        return ApiResponse::success([
            'cuposOcupados' => $cuposOcupados,
            'cuposDisponibles' => $cuposDisponibles,
            'maxInscritos' => $cursoMateria->maxInscritos,
            'estudiantes' => $estudiantes
        ], 'Inscripciones cargadas correctamente.');
    }

    public function docentesDisponibles(\Illuminate\Http\Request $request): JsonResponse
    {
        $idPeriodo = (int) $request->query('idPeriodo');
        $idHorario1 = $request->query('idHorario1') ? (int) $request->query('idHorario1') : null;
        $idHorario2 = $request->query('idHorario2') ? (int) $request->query('idHorario2') : null;
        $idHorario3 = $request->query('idHorario3') ? (int) $request->query('idHorario3') : null;

        $docentes = $this->cursoService->docentesDisponibles($idPeriodo, $idHorario1, $idHorario2, $idHorario3);

        return ApiResponse::success(
            ['docentes' => $docentes->values()],
            'Docentes disponibles cargados correctamente.'
        );
    }

    public function destroy($idCursoMateria): JsonResponse
    {
        $updated = $this->cursoService->destroy($idCursoMateria);

        return ApiResponse::success(
            ['curso' => $updated],
            'Curso deshabilitado correctamente.'
        );
    }

    public function enable($idCursoMateria): JsonResponse
    {
        try {
            $updated = $this->cursoService->enable($idCursoMateria);

            return ApiResponse::success(
                ['curso' => $updated],
                'Curso habilitado correctamente.'
            );
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), null, 422);
        }
    }

    public function quitarEstudiante($idInscripcion): JsonResponse
    {
        try {
            $inscripcion = \App\Models\Inscripcion::findOrFail($idInscripcion);
            $inscripcion->estado = 0;
            $inscripcion->save();

            return ApiResponse::success(
                null,
                'Estudiante retirado del curso correctamente.'
            );
        } catch (\Throwable $e) {
            return ApiResponse::error(
                $e->getMessage() ?: 'No se pudo retirar al estudiante del curso.',
                null,
                422
            );
        }
    }
}
