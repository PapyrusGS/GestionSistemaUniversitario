<?php

namespace App\Repositories\Contracts;

interface ReporteDocenteRepositoryInterface
{
    public function obtenerFiltros(int $docenteId): array;
    public function obtenerReporteSemestre(int $docenteId, int $periodoId): array;
    public function obtenerEstadisticasAprobacion(int $docenteId, int $periodoId, string $materiaId): array;
}
