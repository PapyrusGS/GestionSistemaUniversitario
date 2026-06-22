<?php

namespace App\Services;

use App\Repositories\Contracts\RendimientoRepositoryInterface;

class RendimientoService
{
    public function __construct(
        private readonly RendimientoRepositoryInterface $rendimientoRepository
    ) {
    }

    public function getResumenCurso(int $docenteId, int $cursoMateriaId): array
    {
        $resumen = $this->rendimientoRepository->getResumenCurso($docenteId, $cursoMateriaId);

        return [
            'aprobados'   => (int) $resumen['aprobados'],
            'reprobados'  => (int) $resumen['reprobados'],
            'promedio'    => (float) $resumen['promedio'],
            'total_notas' => (int) $resumen['total_notas']
        ];
    }
}
