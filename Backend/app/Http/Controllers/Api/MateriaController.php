<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MateriaRequest;
use App\Support\ApiResponse;
use App\Services\MateriaService;
use Illuminate\Http\JsonResponse;

class MateriaController extends Controller
{
    public function __construct(
        private readonly MateriaService $materiaService,
    ) {
    }

    public function index(): JsonResponse
    {
        return ApiResponse::success(
            ['materias' => $this->materiaService->index()],
            'Materias cargadas correctamente.'
        );
    }

    public function formData(): JsonResponse
    {
        return ApiResponse::success(
            $this->materiaService->formData(),
            'Datos de formulario cargados correctamente.'
        );
    }

    public function store(MateriaRequest $request): JsonResponse
    {
        $materia = $this->materiaService->store($request->validated());

        return ApiResponse::success(
            ['materia' => $materia],
            'Materia registrada correctamente.',
            201
        );
    }

    public function update(MateriaRequest $request, string $materia): JsonResponse
    {
        $updated = $this->materiaService->update($materia, $request->validated());

        return ApiResponse::success(
            ['materia' => $updated],
            'Materia actualizada correctamente.'
        );
    }

    public function destroy(string $materia): JsonResponse
    {
        $updated = $this->materiaService->destroy($materia);

        return ApiResponse::success(
            ['materia' => $updated],
            'Materia deshabilitada correctamente.'
        );
    }
}
