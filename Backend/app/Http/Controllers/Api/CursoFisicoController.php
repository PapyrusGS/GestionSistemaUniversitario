<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CursoFisicoRequest;
use App\Repositories\Contracts\CursoFisicoRepositoryInterface;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class CursoFisicoController extends Controller
{
    public function __construct(
        private readonly CursoFisicoRepositoryInterface $cursos,
    ) {
    }

    public function index(): JsonResponse
    {
        return ApiResponse::success(
            ['cursos' => $this->cursos->allOrdered()->values()],
            'Aulas cargadas correctamente.'
        );
    }

    public function store(CursoFisicoRequest $request): JsonResponse
    {
        try {
            $curso = $this->cursos->create($request->validated());

            return ApiResponse::success(
                ['curso' => $curso],
                'Aula registrada correctamente.',
                201
            );
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), null, 400);
        }
    }

    public function update(CursoFisicoRequest $request, string $idCurso): JsonResponse
    {
        try {
            $curso = $this->cursos->update($idCurso, $request->validated());

            return ApiResponse::success(
                ['curso' => $curso],
                'Aula actualizada correctamente.'
            );
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), null, 400);
        }
    }

    public function destroy(string $idCurso): JsonResponse
    {
        try {
            $curso = $this->cursos->disable($idCurso);

            return ApiResponse::success(
                ['curso' => $curso],
                'Aula deshabilitada correctamente.'
            );
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), null, 400);
        }
    }

    public function enable(string $idCurso): JsonResponse
    {
        try {
            $curso = $this->cursos->enable($idCurso);

            return ApiResponse::success(
                ['curso' => $curso],
                'Aula habilitada correctamente.'
            );
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), null, 400);
        }
    }

    public function horario(string $idCurso): JsonResponse
    {
        try {
            $schedules = \Illuminate\Support\Facades\DB::table('cursos_materias as cm')
                ->join('materias as m', 'm.idMateria', '=', 'cm.idMateria')
                ->join('carreras as c', 'c.idCarrera', '=', 'm.idCarrera')
                ->join('usuarios as u', 'u.idUsuario', '=', 'cm.idDocente')
                ->join('periodos as p', 'p.idPeriodo', '=', 'cm.idPeriodo')
                ->join('horariocurso as hc', 'hc.idCursoMateria', '=', 'cm.idCursoMateria')
                ->join('horarios as h', 'h.idHorario', '=', 'hc.idHorario')
                ->where('cm.idCurso', $idCurso)
                ->where('cm.estado', 1)
                ->where('p.estado', 1)
                ->select([
                    'h.diaSemana',
                    'h.horaInicio',
                    'h.horaFin',
                    'm.nombre as materia',
                    'p.nombre as periodo',
                    \Illuminate\Support\Facades\DB::raw("TRIM(CONCAT(u.nombre1, ' ', COALESCE(u.nombre2, ''), ' ', u.apellido1, ' ', COALESCE(u.apellido2, ''))) as docente"),
                    'c.nombre as carrera'
                ])
                ->orderBy('h.diaSemana')
                ->orderBy('h.horaInicio')
                ->get();

            return ApiResponse::success(
                ['schedules' => $schedules],
                'Horario de aula cargado correctamente.'
            );
        } catch (\Throwable $e) {
            return ApiResponse::error(
                $e->getMessage() ?: 'No se pudo cargar el horario del aula.',
                null,
                400
            );
        }
    }
}
