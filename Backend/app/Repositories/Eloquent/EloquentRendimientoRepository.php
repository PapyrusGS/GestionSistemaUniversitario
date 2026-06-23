<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RendimientoRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EloquentRendimientoRepository implements RendimientoRepositoryInterface
{
    public function getResumenCurso(int $docenteId, int $cursoMateriaId): array
    {
        $rows = DB::select('CALL sp_docente_rendimiento(?, ?)', [
            $docenteId,
            $cursoMateriaId
        ]);

        if (empty($rows)) {
            throw (new ModelNotFoundException())->setModel('Rendimiento');
        }

        return (array) $rows[0];
    }
}
