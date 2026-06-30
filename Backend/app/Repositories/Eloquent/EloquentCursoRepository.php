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

    public function activePhysicalForSelect(): Collection
    {
        return collect(DB::select('CALL sp_cursos_active()'))->map(fn ($row) => (array) $row);
    }

    public function create(array $data): array
    {
        $rows = DB::select('CALL sp_cursos_store(?, ?, ?, ?, ?, ?, ?)', [
            $data['idCurso'],
            $data['idMateria'],
            $data['idDocente'],
            $data['idHorario1'],
            $data['idHorario2'] ?? null,
            $data['idHorario3'] ?? null,
            $data['idPeriodo'],
        ]);

        if ($rows === []) {
            throw (new ModelNotFoundException())->setModel('Curso');
        }

        return (array) $rows[0];
    }

    public function disable($id): array
    {
        $rows = DB::select('CALL sp_cursos_disable(?)', [$id]);

        if ($rows === []) {
            throw (new ModelNotFoundException())->setModel('CursoMateria', [$id]);
        }

        return (array) $rows[0];
    }

    public function enable($id): array
    {
        DB::statement('UPDATE cursos_materias SET estado = 1, updated_at = NOW() WHERE idCursoMateria = ?', [$id]);

        $rows = DB::select('CALL sp_cursos_find(?)', [$id]);

        if ($rows === []) {
            throw (new ModelNotFoundException())->setModel('CursoMateria', [$id]);
        }

        return (array) $rows[0];
    }
}
