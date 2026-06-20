<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CarreraRequest;
use App\Support\ApiResponse;
use App\Services\CarreraService;
use Illuminate\Http\JsonResponse;

class CarreraController extends Controller
{
    public function __construct(
        private readonly CarreraService $carreraService,
    ) {
    }

    public function index(): JsonResponse
    {
        return ApiResponse::success(
            ['carreras' => $this->carreraService->index()],
            'Carreras cargadas correctamente.'
        );
    }

    public function store(CarreraRequest $request): JsonResponse
    {
        $carrera = $this->carreraService->store($request->validated());

        return ApiResponse::success(
            ['carrera' => $carrera],
            'Carrera registrada correctamente.',
            201
        );
    }

    public function update(CarreraRequest $request, int $carrera): JsonResponse
    {
        $carreraUpdated = $this->carreraService->update($carrera, $request->validated());

        return ApiResponse::success(
            ['carrera' => $carreraUpdated],
            'Carrera actualizada correctamente.'
        );
    }

    public function destroy(int $carrera): JsonResponse
    {
        $carreraUpdated = $this->carreraService->destroy($carrera);

        return ApiResponse::success(
            ['carrera' => $carreraUpdated],
            'Carrera deshabilitada correctamente.'
        );
    }
}
