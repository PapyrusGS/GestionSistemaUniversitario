<?php

namespace App\Repositories\Contracts;

interface RendimientoRepositoryInterface
{
    public function getResumenCurso(int $docenteId, int $cursoMateriaId): array;
}
