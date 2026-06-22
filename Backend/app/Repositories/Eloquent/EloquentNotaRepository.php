<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\NotaRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class EloquentNotaRepository implements NotaRepositoryInterface
{
    public function create(array $data): array
    {
        $rows = DB::select('CALL sp_registrar_nota(?, ?, ?)', [
            $data['docente_id'],
            $data['estudiante_materia_id'],
            $data['nota'],
        ]);

        if (empty($rows)) {
            throw (new ModelNotFoundException())->setModel('Nota');
        }

        return (array) $rows[0];
    }

    public function updateNota(int $docenteId, int $notaId, float $nota): array
    {
        $rows = DB::select('CALL sp_editar_nota(?, ?, ?)', [
            $docenteId,
            $notaId,
            $nota,
        ]);

        if (empty($rows)) {
            throw (new ModelNotFoundException())->setModel('Nota');
        }

        return (array) $rows[0];
    }
}
