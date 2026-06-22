<?php

namespace App\Services;

use App\Repositories\Contracts\ReporteNotasRepositoryInterface;

class ReporteNotasService
{
    public function __construct(
        private readonly ReporteNotasRepositoryInterface $reporteRepository
    ) {
    }

    public function obtenerReporte(int $docenteId, int $cursoMateriaId): array
    {
        $rawRows = $this->reporteRepository->obtenerReporte($docenteId, $cursoMateriaId);

        if (empty($rawRows)) {
            return [
                'summary' => [
                    'total_estudiantes' => 0,
                    'total_con_nota'    => 0,
                    'promedio_general'  => 0.0,
                ],
                'rows' => []
            ];
        }

        // Obtener el summary del primer elemento (ya que es igual en todos los registros devueltos por el SP)
        $first = $rawRows[0];
        $summary = [
            'total_estudiantes' => (int) $first['total_estudiantes'],
            'total_con_nota'    => (int) $first['total_con_nota'],
            'promedio_general'  => (float) $first['promedio_general']
        ];

        // Mapear solo los campos de estudiantes en rows
        $rows = array_map(function ($row) {
            return [
                'idInscripcion'   => (int) $row['idInscripcion'],
                'nombreCompleto'  => $row['nombreCompleto'],
                'nota'            => $row['nota'] !== null ? (float) $row['nota'] : null,
                'estadoAcademico' => $row['estadoAcademico']
            ];
        }, $rawRows);

        return [
            'summary' => $summary,
            'rows'    => $rows
        ];
    }
}
