<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StudentService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    public function __construct(
        private readonly StudentService $studentService,
    ) {
    }

    public function dashboard(Request $request): JsonResponse
    {
        $student = $this->studentService->getStudentOrFail($request);
        $data = $this->studentService->dashboard($student);

        return ApiResponse::success($data, 'Dashboard del estudiante.');
    }

    public function materiasDisponibles(Request $request): JsonResponse
    {
        $student = $this->studentService->getStudentOrFail($request);
        $career = $this->studentService->getStudentCareer($student);

        if (! $career) {
            return ApiResponse::success([
                'data' => [],
            ], 'El estudiante no tiene una carrera activa asignada.');
        }

        $subjects = $this->studentService->materiasDisponibles($student);

        return ApiResponse::success([
            'data' => $subjects,
            'message' => $subjects->isEmpty() ? 'No existen materias disponibles para inscripcion.' : null,
        ], 'Materias disponibles.');
    }

    public function inscribir(Request $request): JsonResponse
    {
        $student = $this->studentService->getStudentOrFail($request);
        $validated = $request->validate([
            'idCursoMateria' => ['required', 'integer', 'exists:cursos_materias,idCursoMateria'],
        ]);

        $idInscripcion = $this->studentService->inscribir($student, (int) $validated['idCursoMateria']);

        return ApiResponse::success([
            'idInscripcion' => $idInscripcion,
        ], 'Inscripcion registrada correctamente.', 201);
    }

    public function inscripciones(Request $request): JsonResponse
    {
        $student = $this->studentService->getStudentOrFail($request);
        $items = $this->studentService->inscripciones($student);

        return ApiResponse::success([
            'data' => $items,
            'message' => $items->isEmpty() ? 'No tienes materias inscritas activas.' : null,
        ], 'Materias inscritas.');
    }

    public function notas(Request $request): JsonResponse
    {
        $student = $this->studentService->getStudentOrFail($request);

        $filters = array_filter([
            'search' => $request->query('search'),
            'semestre' => $request->query('semestre'),
            'periodo' => $request->query('periodo'),
        ]);

        $grades = $this->studentService->notas($student, $filters);

        return ApiResponse::success([
            'data' => $grades,
            'message' => $grades->isEmpty() ? 'Aun no existen calificaciones registradas.' : null,
        ], 'Calificaciones del estudiante.');
    }

    public function historial(Request $request): JsonResponse
    {
        $student = $this->studentService->getStudentOrFail($request);
        $history = $this->studentService->historial($student);

        return ApiResponse::success([
            'data' => $history,
            'message' => $history->isEmpty() ? 'No existe historial academico para mostrar.' : null,
        ], 'Historial academico.');
    }

    public function malla(Request $request): JsonResponse
    {
        $student = $this->studentService->getStudentOrFail($request);
        $curriculum = $this->studentService->malla($student);

        return ApiResponse::success([
            'data' => $curriculum,
            'message' => $curriculum->isEmpty() ? 'No hay malla curricular asociada al estudiante.' : null,
        ], 'Malla curricular.');
    }

    public function periodos(Request $request): JsonResponse
    {
        $periodos = \App\Models\Periodo::orderByDesc('idPeriodo')->get(['idPeriodo', 'nombre']);

        return ApiResponse::success($periodos, 'Listado de periodos.');
    }

    public function reporte(Request $request): JsonResponse
    {
        $student = $this->studentService->getStudentOrFail($request);
        $validated = $request->validate([
            'tipo' => ['required', 'in:inscripciones,notas,historial'],
        ]);

        $data = $this->studentService->reporte($request, $student, $validated['tipo']);

        return ApiResponse::success([
            'tipo' => $validated['tipo'],
            'generadoEn' => now()->format('Y-m-d H:i:s'),
            'estudiante' => $student->nombreCompleto,
            'data' => $data,
            'message' => $data->isEmpty() ? 'No existe informacion para el reporte seleccionado.' : null,
        ], 'Reporte generado.');
    }

    private function obtenerDatosReporte(Request $request): array
    {
        $validated = $request->validate([
            'tipo' => ['required', 'in:inscripciones,notas,historial'],
        ]);

        $student = $this->studentService->getStudentOrFail($request);
        $data = $this->studentService->reporte($request, $student, $validated['tipo']);

        return $data->toArray();
    }

    public function exportCsv(Request $request)
    {
        $datos = $this->obtenerDatosReporte($request);

        $tipo    = $request->input('tipo', 'reporte');
        $periodo = $request->input('periodo', 'todos');

        if (empty($datos)) {
            return response()->json(['message' => 'No hay datos para exportar.'], 404);
        }

        $headers = array_keys((array) $datos[0]);
        $csv = implode(',', $headers) . "\n";
        foreach ($datos as $fila) {
            $csv .= implode(',', array_map(
                fn($v) => '"' . str_replace('"', '""', $v ?? '') . '"',
                (array) $fila
            )) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"reporte-{$tipo}-{$periodo}.csv\"",
        ]);
    }

    public function exportPdf(Request $request)
    {
        $datos   = $this->obtenerDatosReporte($request);
        $tipo    = $request->input('tipo', 'reporte');
        $periodo = $request->input('periodo', 'todos');
        $usuario = $request->user()->nombreCompleto ?? 'Estudiante';

        if (empty($datos)) {
            return response()->json(['message' => 'No hay datos para generar el PDF.'], 404);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reportes.estudiante', [
            'datos'   => $datos,
            'tipo'    => $tipo,
            'periodo' => $periodo,
            'usuario' => $usuario,
        ]);

        return $pdf->download("reporte-{$tipo}-{$periodo}.pdf");
    }
}
