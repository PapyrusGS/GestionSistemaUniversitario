<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\CursoRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentCursoRepository implements CursoRepositoryInterface
{
    public function allOrdered(): Collection
    {
        return collect(DB::select('CALL sp_cursos_list()'))->map(fn ($row) => (array) $row);
    }

    public function create(array $data): array
    {
        $rows = DB::select('CALL sp_cursos_store(?, ?, ?, ?, ?)', [
            $data['idMateria'],
            $data['idDocente'],
            $data['idHorario'],
            $data['idPeriodo'],
            $data['maxInscritos'],
        ]);

        if ($rows === []) {
            throw (new ModelNotFoundException())->setModel('Curso');
        }

        return (array) $rows[0];
    }
}
