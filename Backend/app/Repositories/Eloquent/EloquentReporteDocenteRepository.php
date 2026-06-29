<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ReporteDocenteRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EloquentReporteDocenteRepository implements ReporteDocenteRepositoryInterface
{
    public function obtenerFiltros(int $docenteId): array
    {
        return DB::select('CALL sp_docente_filtros(?)', [$docenteId]);
    }

    public function obtenerReporteSemestre(int $docenteId, int $periodoId): array
    {
        return DB::select('CALL sp_docente_reporte_semestre(?, ?)', [$docenteId, $periodoId]);
    }

    public function obtenerEstadisticasAprobacion(int $docenteId, int $periodoId, string $materiaId): array
    {
        return DB::select('CALL sp_docente_estadisticas_aprobacion(?, ?, ?)', [$docenteId, $periodoId, $materiaId]);
    }
}
