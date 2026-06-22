<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ReporteNotasRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EloquentReporteNotasRepository implements ReporteNotasRepositoryInterface
{
    public function obtenerReporte(int $docenteId, int $cursoMateriaId): array
    {
        $rows = DB::select('CALL sp_docente_reporte_notas(?, ?)', [
            $docenteId,
            $cursoMateriaId
        ]);

        return array_map(fn($item) => (array) $item, $rows);
    }
}
