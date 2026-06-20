<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CarreraRequest;
use App\Models\Carrera;
use Illuminate\Http\JsonResponse;

class CarreraController extends Controller
{
    /**
     * Display a listing of all careers (both active and inactive).
     */
    public function index(): JsonResponse
    {
        $carreras = Carrera::query()
            ->orderByDesc('estado')   // Active first
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'carreras' => $carreras->map(fn (Carrera $c) => $this->payload($c)),
        ]);
    }

    /**
     * Store a newly created career.
     */
    public function store(CarreraRequest $request): JsonResponse
    {
        $carrera = Carrera::query()->create([
            'nombre'      => $request->validated()['nombre'],
            'descripcion' => $request->validated()['descripcion'] ?? null,
            'estado'      => true,
            'fechaRegistro' => now(),
        ]);

        return response()->json([
            'message' => 'Carrera registrada correctamente.',
            'carrera' => $this->payload($carrera),
        ], 201);
    }

    /**
     * Update the specified career.
     */
    public function update(CarreraRequest $request, int $carrera): JsonResponse
    {
        $carreraModel = Carrera::query()->findOrFail($carrera);

        $carreraModel->update([
            'nombre'      => $request->validated()['nombre'],
            'descripcion' => $request->validated()['descripcion'] ?? $carreraModel->descripcion,
        ]);

        return response()->json([
            'message' => 'Carrera actualizada correctamente.',
            'carrera' => $this->payload($carreraModel->fresh()),
        ]);
    }

    /**
     * Soft-disable the specified career (logical delete: estado = 0).
     * Physical DELETE is intentionally NOT used to preserve historical data.
     */
    public function destroy(int $carrera): JsonResponse
    {
        $carreraModel = Carrera::query()->findOrFail($carrera);

        if (! $carreraModel->estado) {
            return response()->json([
                'message' => 'La carrera ya se encuentra deshabilitada.',
            ], 422);
        }

        $carreraModel->update(['estado' => false]);

        return response()->json([
            'message' => 'Carrera deshabilitada correctamente. Los registros históricos han sido conservados.',
            'carrera' => $this->payload($carreraModel->fresh()),
        ]);
    }

    /**
     * Build consistent response payload for a single career.
     */
    private function payload(Carrera $carrera): array
    {
        return [
            'idCarrera'     => $carrera->idCarrera,
            'nombre'        => $carrera->nombre,
            'descripcion'   => $carrera->descripcion,
            'estado'        => (bool) $carrera->estado,
            'fechaRegistro' => $carrera->fechaRegistro?->format('Y-m-d H:i'),
        ];
    }
}
