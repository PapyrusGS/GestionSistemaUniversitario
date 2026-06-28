<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use App\Services\ReporteNotasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteNotasController extends Controller
{
    public function __construct(
        private readonly ReporteNotasService $reporteService
    ) {
    }

    public function obtenerReporteJSON(Request $request, int $idCursoMateria): JsonResponse
    {
        try {
            $docenteId = $request->user()->idUsuario;
            $reporte = $this->reporteService->obtenerReporte($docenteId, $idCursoMateria);
            return ApiResponse::success($reporte, 'Reporte de notas generado.');
        } catch (QueryException $e) {
            $errorInfo = $e->errorInfo;
            $message = isset($errorInfo[2]) ? $errorInfo[2] : $e->getMessage();
            return ApiResponse::error($message, null, 400);
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), null, 400);
        }
    }

    public function exportarPDF(Request $request, int $idCursoMateria)
    {
        try {
            $user = $request->user();
            $docenteId = $user->idUsuario;

            // 1. Obtener datos del reporte
            $reporte = $this->reporteService->obtenerReporte($docenteId, $idCursoMateria);

            // 2. Cargar detalles del curso para el encabezado (reutilizando query nativa rápida)
            $cursoData = DB::select('
                SELECT cm.idCurso, m.nombre AS materia_nombre, 
                       COALESCE(
                           (SELECT GROUP_CONCAT(DISTINCT CONCAT("Dia ", h.diaSemana, " ", TIME_FORMAT(h.horaInicio, "%H:%i"), "-", TIME_FORMAT(h.horaFin, "%H:%i")) SEPARATOR ", ")
                            FROM horariocurso hc
                            INNER JOIN horarios h ON h.idHorario = hc.idHorario
                            WHERE hc.idCursoMateria = cm.idCursoMateria), 
                           "Sin Horario"
                       ) AS turno_nombre
                FROM cursos_materias cm
                INNER JOIN materias m ON m.idMateria = cm.idMateria
                WHERE cm.idCursoMateria = ?
            ', [$idCursoMateria]);

            $cursoNombre = $cursoData[0]->materia_nombre ?? 'Curso';
            $idCurso = $cursoData[0]->idCurso ?? 'S-N';
            $turnoNombre = $cursoData[0]->turno_nombre ?? 'Sin Horario';

            $data = [
                'fecha'          => now()->format('Y-m-d H:i'),
                'docente_nombre' => trim($user->nombre1 . ' ' . $user->nombre2 . ' ' . $user->apellido1 . ' ' . $user->apellido2),
                'curso_nombre'   => $cursoNombre,
                'turno_nombre'   => $turnoNombre,
                'summary'        => $reporte['summary'],
                'rows'           => $reporte['rows']
            ];

            // 3. Generar PDF usando DomPDF
            $pdf = Pdf::loadView('pdf.reporte_notas', $data);

            $fechaArchivo = now()->format('Y_m_d');
            $nombreArchivo = "reporte_notas_curso_{$idCurso}_{$fechaArchivo}.pdf";

            return $pdf->download($nombreArchivo);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error al generar el PDF: ' . $e->getMessage()], 400);
        }
    }

    public function exportarExcel(Request $request, int $idCursoMateria): StreamedResponse
    {
        $user = $request->user();
        $docenteId = $user->idUsuario;

        // 1. Obtener datos
        $reporte = $this->reporteService->obtenerReporte($docenteId, $idCursoMateria);

        // 2. Obtener idCurso para el nombre del archivo
        $cursoData = DB::select('SELECT idCurso FROM cursos_materias WHERE idCursoMateria = ?', [$idCursoMateria]);
        $idCurso = $cursoData[0]->idCurso ?? 'S-N';

        $fechaArchivo = now()->format('Y_m_d');
        $nombreArchivo = "reporte_notas_curso_{$idCurso}_{$fechaArchivo}.csv";

        // 3. Stream de respuesta CSV real
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
        ];

        return response()->stream(function () use ($reporte) {
            $handle = fopen('php://output', 'w');

            // Agregar BOM UTF-8 para visualización correcta en Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Agregar Encabezados
            fputcsv($handle, ['ID Inscripcion', 'Estudiante', 'Nota', 'Estado']);

            // Agregar Datos
            foreach ($reporte['rows'] as $row) {
                fputcsv($handle, [
                    $row['idInscripcion'],
                    $row['nombreCompleto'],
                    $row['nota'] !== null ? number_format($row['nota'], 1) : '',
                    $row['estadoAcademico']
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
