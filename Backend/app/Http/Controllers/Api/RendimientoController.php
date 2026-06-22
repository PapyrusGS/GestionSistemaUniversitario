<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use App\Services\RendimientoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class RendimientoController extends Controller
{
    public function __construct(
        private readonly RendimientoService $rendimientoService
    ) {
    }

    public function obtenerRendimiento(Request $request, int $idCursoMateria): JsonResponse
    {
        try {
            $docenteId = $request->user()->idUsuario;
            $rendimiento = $this->rendimientoService->getResumenCurso($docenteId, $idCursoMateria);

            $mensaje = $rendimiento['total_notas'] === 0 
                ? 'No existen calificaciones registradas.' 
                : 'Rendimiento académico cargado con éxito.';

            return ApiResponse::success($rendimiento, $mensaje);
        } catch (QueryException $e) {
            $errorInfo = $e->errorInfo;
            $message = isset($errorInfo[2]) ? $errorInfo[2] : $e->getMessage();
            return ApiResponse::error($message, null, 400);
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), null, 400);
        }
    }
}
