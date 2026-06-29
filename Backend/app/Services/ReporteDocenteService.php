<?php

namespace App\Services;

use App\Repositories\Contracts\ReporteDocenteRepositoryInterface;

class ReporteDocenteService
{
    public function __construct(
        private readonly ReporteDocenteRepositoryInterface $repository,
    ) {
    }

    /**
     * Obtener filtros dinámicos (periodos y materias del docente).
     */
    public function obtenerFiltros(int $docenteId): array
    {
        $raw = $this->repository->obtenerFiltros($docenteId);

        $periodos = [];
        $materias = [];

        foreach ($raw as $row) {
            if ($row->tipo === 'periodo') {
                $periodos[] = [
                    'id' => $row->id,
                    'nombre' => $row->valor,
                ];
            } elseif ($row->tipo === 'materia') {
                $materias[] = [
                    'id' => $row->id,
                    'nombre' => $row->valor,
                ];
            }
        }

        return [
            'periodos' => $periodos,
            'materias' => $materias,
        ];
    }

    /**
     * Obtener reporte de estudiantes por semestre (HU-DOC-08).
     */
    public function obtenerReporteSemestre(int $docenteId, int $periodoId): array
    {
        $rows = $this->repository->obtenerReporteSemestre($docenteId, $periodoId);

        if (empty($rows)) {
            return [
                'summary' => [
                    'total_estudiantes' => 0,
                    'total_aprobados' => 0,
                    'total_reprobados' => 0,
                    'promedio_general' => 0,
                ],
                'rows' => [],
            ];
        }

        // Extraer totales del primer registro (son iguales en todos)
        $first = $rows[0];
        $summary = [
            'total_estudiantes' => (int) $first->total_estudiantes,
            'total_aprobados' => (int) $first->total_aprobados,
            'total_reprobados' => (int) $first->total_reprobados,
            'promedio_general' => round((float) $first->promedio_general, 2),
        ];

        // Limpiar campos inline de las filas de detalle
        $detalle = array_map(function ($row) {
            return [
                'idInscripcion' => $row->idInscripcion,
                'idEstudiante' => $row->idEstudiante,
                'nombreCompleto' => $row->nombreCompleto,
                'materia_nombre' => $row->materia_nombre,
                'nota' => $row->nota,
                'estadoAcademico' => $row->estadoAcademico,
            ];
        }, $rows);

        return [
            'summary' => $summary,
            'rows' => $detalle,
        ];
    }

    /**
     * Obtener estadísticas de aprobados/reprobados (HU-DOC-10).
     */
    public function obtenerEstadisticas(int $docenteId, int $periodoId, string $materiaId): array
    {
        $rows = $this->repository->obtenerEstadisticasAprobacion($docenteId, $periodoId, $materiaId);

        // Calcular resumen global
        $totalAprobados = 0;
        $totalReprobados = 0;
        $totalNotas = 0;
        $sumaNotas = 0;

        $detalle = [];
        foreach ($rows as $row) {
            $aprobados = (int) $row->aprobados;
            $reprobados = (int) $row->reprobados;
            $notas = (int) $row->total_notas;

            $totalAprobados += $aprobados;
            $totalReprobados += $reprobados;
            $totalNotas += $notas;
            $sumaNotas += (float) $row->promedio_general * $notas;

            $detalle[] = [
                'idMateria' => $row->idMateria,
                'materia_nombre' => $row->materia_nombre,
                'idPeriodo' => $row->idPeriodo,
                'periodo_nombre' => $row->periodo_nombre,
                'aprobados' => $aprobados,
                'reprobados' => $reprobados,
                'total_notas' => $notas,
                'porcentaje_aprobacion' => round((float) $row->porcentaje_aprobacion, 2),
                'promedio_general' => round((float) $row->promedio_general, 2),
            ];
        }

        $promedioGlobal = $totalNotas > 0 ? round($sumaNotas / $totalNotas, 2) : 0;
        $porcentajeGlobal = $totalNotas > 0 ? round(($totalAprobados / $totalNotas) * 100, 2) : 0;

        return [
            'summary' => [
                'total_aprobados' => $totalAprobados,
                'total_reprobados' => $totalReprobados,
                'porcentaje_aprobacion' => $porcentajeGlobal,
                'promedio_general' => $promedioGlobal,
            ],
            'rows' => $detalle,
        ];
    }
}
