<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use App\Services\ReporteDocenteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReporteDocenteController extends Controller
{
    public function __construct(
        private readonly ReporteDocenteService $service,
    ) {
    }

    /**
     * GET /docente/reportes/filtros
     * Retorna periodos y materias asociadas al docente autenticado.
     */
    public function filtros(Request $request): JsonResponse
    {
        try {
            $docenteId = $request->user()->idUsuario;
            $filtros = $this->service->obtenerFiltros($docenteId);

            return ApiResponse::success($filtros, 'Filtros cargados correctamente.');
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), null, 400);
        }
    }

    /**
     * GET /docente/reportes/semestre/{idPeriodo}
     * Retorna el reporte de estudiantes por semestre (HU-DOC-08).
     */
    public function reporteSemestre(Request $request, int $idPeriodo): JsonResponse
    {
        try {
            $docenteId = $request->user()->idUsuario;
            $reporte = $this->service->obtenerReporteSemestre($docenteId, $idPeriodo);

            if (empty($reporte['rows'])) {
                return ApiResponse::success($reporte, 'No hay datos disponibles para el semestre seleccionado.');
            }

            return ApiResponse::success($reporte, 'Reporte semestral generado correctamente.');
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), null, 400);
        }
    }

    /**
     * GET /docente/reportes/estadisticas
     * Retorna estadísticas de aprobados/reprobados (HU-DOC-10).
     * Query params opcionales: idPeriodo, idMateria
     */
    public function estadisticas(Request $request): JsonResponse
    {
        try {
            $docenteId = $request->user()->idUsuario;
            $periodoId = (int) $request->query('idPeriodo', 0);
            $materiaId = (string) $request->query('idMateria', '');

            $estadisticas = $this->service->obtenerEstadisticas($docenteId, $periodoId, $materiaId);

            if (empty($estadisticas['rows'])) {
                return ApiResponse::success($estadisticas, 'No hay datos estadísticos para los filtros seleccionados.');
            }

            return ApiResponse::success($estadisticas, 'Estadísticas generadas correctamente.');
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), null, 400);
        }
    }

    /**
     * GET /docente/reportes/semestre/{idPeriodo}/pdf
     * Exporta el reporte de estudiantes por semestre a PDF.
     */
    public function exportarSemestrePDF(Request $request, int $idPeriodo)
    {
        try {
            $user = $request->user();
            $docenteId = $user->idUsuario;

            $reporte = $this->service->obtenerReporteSemestre($docenteId, $idPeriodo);

            $periodoData = \Illuminate\Support\Facades\DB::select('SELECT nombre FROM periodos WHERE idPeriodo = ?', [$idPeriodo]);
            $periodoNombre = $periodoData[0]->nombre ?? 'Semestre';

            $data = [
                'fecha'          => now()->format('Y-m-d H:i'),
                'docente_nombre' => trim($user->nombre1 . ' ' . $user->nombre2 . ' ' . $user->apellido1 . ' ' . $user->apellido2),
                'periodo_nombre' => $periodoNombre,
                'summary'        => $reporte['summary'],
                'rows'           => $reporte['rows']
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reporte_semestre', $data);

            $fechaArchivo = now()->format('Y_m_d');
            $nombreArchivo = "reporte_notas_semestre_{$idPeriodo}_{$fechaArchivo}.pdf";

            return $pdf->download($nombreArchivo);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error al generar el PDF: ' . $e->getMessage()], 400);
        }
    }

    /**
     * GET /docente/reportes/semestre/{idPeriodo}/excel
     * Exporta el reporte de estudiantes por semestre a CSV.
     */
    public function exportarSemestreExcel(Request $request, int $idPeriodo): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = $request->user();
        $docenteId = $user->idUsuario;

        $reporte = $this->service->obtenerReporteSemestre($docenteId, $idPeriodo);

        $fechaArchivo = now()->format('Y_m_d');
        $nombreArchivo = "reporte_notas_semestre_{$idPeriodo}_{$fechaArchivo}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
        ];

        return response()->stream(function () use ($reporte) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['Estudiante', 'Materia', 'Nota', 'Estado']);

            foreach ($reporte['rows'] as $row) {
                fputcsv($handle, [
                    $row['nombreCompleto'],
                    $row['materia_nombre'],
                    $row['nota'] !== null ? number_format($row['nota'], 1) : '',
                    $row['estadoAcademico']
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * GET /docente/reportes/estadisticas/pdf
     * Exporta las estadísticas de aprobación a PDF.
     */
    public function exportarEstadisticasPDF(Request $request)
    {
        try {
            $user = $request->user();
            $docenteId = $user->idUsuario;
            $periodoId = (int) $request->query('idPeriodo', 0);
            $materiaId = (string) $request->query('idMateria', '');

            $estadisticas = $this->service->obtenerEstadisticas($docenteId, $periodoId, $materiaId);

            $filtroPeriodo = 'Todos';
            if ($periodoId > 0) {
                $pData = \Illuminate\Support\Facades\DB::select('SELECT nombre FROM periodos WHERE idPeriodo = ?', [$periodoId]);
                $filtroPeriodo = $pData[0]->nombre ?? 'Todos';
            }

            $filtroMateria = 'Todas';
            if ($materiaId) {
                $mData = \Illuminate\Support\Facades\DB::select('SELECT nombre FROM materias WHERE idMateria = ?', [$materiaId]);
                $filtroMateria = $mData[0]->nombre ?? 'Todas';
            }

            $data = [
                'fecha'          => now()->format('Y-m-d H:i'),
                'docente_nombre' => trim($user->nombre1 . ' ' . $user->nombre2 . ' ' . $user->apellido1 . ' ' . $user->apellido2),
                'filtro_periodo' => $filtroPeriodo,
                'filtro_materia' => $filtroMateria,
                'summary'        => $estadisticas['summary'],
                'rows'           => $estadisticas['rows']
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reporte_estadisticas', $data);

            $fechaArchivo = now()->format('Y_m_d');
            $nombreArchivo = "reporte_estadisticas_aprobacion_{$fechaArchivo}.pdf";

            return $pdf->download($nombreArchivo);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error al generar el PDF: ' . $e->getMessage()], 400);
        }
    }

    /**
     * GET /docente/reportes/estadisticas/excel
     * Exporta las estadísticas de aprobación a CSV.
     */
    public function exportarEstadisticasExcel(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = $request->user();
        $docenteId = $user->idUsuario;
        $periodoId = (int) $request->query('idPeriodo', 0);
        $materiaId = (string) $request->query('idMateria', '');

        $estadisticas = $this->service->obtenerEstadisticas($docenteId, $periodoId, $materiaId);

        $fechaArchivo = now()->format('Y_m_d');
        $nombreArchivo = "reporte_estadisticas_aprobacion_{$fechaArchivo}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
        ];

        return response()->stream(function () use ($estadisticas) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['Materia', 'Semestre', 'Aprobados', 'Reprobados', 'Total', '% Aprobacion', 'Promedio General']);

            foreach ($estadisticas['rows'] as $row) {
                fputcsv($handle, [
                    $row['materia_nombre'],
                    $row['periodo_nombre'],
                    $row['aprobados'],
                    $row['reprobados'],
                    $row['total_notas'],
                    number_format($row['porcentaje_aprobacion'], 2) . '%',
                    number_format($row['promedio_general'], 2)
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
