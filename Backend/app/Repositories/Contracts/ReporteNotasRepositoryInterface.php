<?php

namespace App\Repositories\Contracts;

interface ReporteNotasRepositoryInterface
{
    public function obtenerReporte(int $docenteId, int $cursoMateriaId): array;
}
