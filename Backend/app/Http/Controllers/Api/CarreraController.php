<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CarreraRequest;
use App\Models\Carrera;
use Illuminate\Http\JsonResponse;

class CarreraController extends Controller
{
    /**
     * Devuelve únicamente las carreras activas (estado = true).
     */
    public function index(): JsonResponse
    {
        $carreras = Carrera::where('estado', true)->get();

        return response()->json([
            'carreras' => $carreras->map(function ($carrera) {
                return [
                    'idCarrera' => $carrera->idCarrera,
                    'nombre' => $carrera->nombre,
                    'descripcion' => $carrera->descripcion,
                    'estado' => (bool)$carrera->estado,
                    'fechaRegistro' => $carrera->fechaRegistro ? $carrera->fechaRegistro->format('Y-m-d H:i') : null,
                ];
            })
        ]);
    }

    /**
     * Registra una nueva carrera.
     */
    public function store(CarreraRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['fechaRegistro'] = now();
        $data['estado'] = true;

        $carrera = Carrera::create($data);

        return response()->json([
            'message' => 'Carrera registrada correctamente.',
            'carrera' => [
                'idCarrera' => $carrera->idCarrera,
                'nombre' => $carrera->nombre,
                'descripcion' => $carrera->descripcion,
                'estado' => (bool)$carrera->estado,
                'fechaRegistro' => $carrera->fechaRegistro ? $carrera->fechaRegistro->format('Y-m-d H:i') : null,
            ]
        ], 201);
    }

    /**
     * Actualiza una carrera existente.
     */
    public function update(CarreraRequest $request, int $id): JsonResponse
    {
        $carrera = Carrera::findOrFail($id);
        $carrera->update($request->validated());

        return response()->json([
            'message' => 'Carrera actualizada correctamente.',
            'carrera' => [
                'idCarrera' => $carrera->idCarrera,
                'nombre' => $carrera->nombre,
                'descripcion' => $carrera->descripcion,
                'estado' => (bool)$carrera->estado,
                'fechaRegistro' => $carrera->fechaRegistro ? $carrera->fechaRegistro->format('Y-m-d H:i') : null,
            ]
        ]);
    }

    /**
     * Deshabilita (borrado lógico) una carrera.
     */
    public function destroy(int $id): JsonResponse
    {
        $carrera = Carrera::findOrFail($id);

        if (!$carrera->estado) {
            return response()->json([
                'message' => 'La carrera ya se encuentra deshabilitada.'
            ], 422);
        }

        $carrera->update(['estado' => false]);

        return response()->json([
            'message' => 'Carrera deshabilitada correctamente.',
            'carrera' => [
                'idCarrera' => $carrera->idCarrera,
                'nombre' => $carrera->nombre,
                'descripcion' => $carrera->descripcion,
                'estado' => (bool)$carrera->estado,
                'fechaRegistro' => $carrera->fechaRegistro ? $carrera->fechaRegistro->format('Y-m-d H:i') : null,
            ]
        ]);
    }
}

