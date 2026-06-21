<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $curso = $this->cursoService->store($request->validated());

        return ApiResponse::success(
            ['curso' => $curso],
            'Curso registrado correctamente.',
            201
        );
    }
}
