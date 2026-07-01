<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\CursoFisicoRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class EloquentCursoFisicoRepository implements CursoFisicoRepositoryInterface
{
    public function allOrdered(array $filters = []): Collection
    {
        return collect(DB::select('CALL sp_cursos_fisicos_list()'))->map(fn ($row) => (array) $row);
    }

    public function active(): Collection
    {
        return collect(DB::select('CALL sp_cursos_active()'))->map(fn ($row) => (array) $row);
    }

    public function create(array $data): array
    {
        $rows = DB::select('CALL sp_cursos_fisicos_store(?, ?)', [
            $data['idCurso'],
            $data['capacidad'],
        ]);

        if ($rows === []) {
            throw (new ModelNotFoundException())->setModel('Curso', [$data['idCurso']]);
        }

        return (array) $rows[0];
    }

    public function update(string $id, array $data): array
    {
        $rows = DB::select('CALL sp_cursos_fisicos_update(?, ?)', [
            $id,
            $data['capacidad'],
        ]);

        if ($rows === []) {
            throw (new ModelNotFoundException())->setModel('Curso', [$id]);
        }

        return (array) $rows[0];
    }

    public function disable(string $id): array
    {
        $rows = DB::select('CALL sp_cursos_fisicos_disable(?)', [$id]);

        if ($rows === []) {
            throw (new ModelNotFoundException())->setModel('Curso', [$id]);
        }

        return (array) $rows[0];
    }

    public function enable(string $id): array
    {
        $rows = DB::select('CALL sp_cursos_fisicos_enable(?)', [$id]);

        if ($rows === []) {
            throw (new ModelNotFoundException())->setModel('Curso', [$id]);
        }

        return (array) $rows[0];
    }

    public function findOrFail(string $id): array
    {
        $rows = DB::select('CALL sp_cursos_fisicos_find(?)', [$id]);

        if ($rows === []) {
            throw (new ModelNotFoundException())->setModel('Curso', [$id]);
        }

        return (array) $rows[0];
    }
}
