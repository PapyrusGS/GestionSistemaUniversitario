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
}
