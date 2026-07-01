<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\MateriaRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class EloquentMateriaRepository implements MateriaRepositoryInterface
{
    public function allOrdered(array $filters = []): Collection
    {
        return collect(DB::select(
            'CALL sp_materias_list(?, ?, ?)',
            [
                $filters['idCarrera'] ?? null,
                $filters['busqueda'] ?? null,
                $filters['semestre'] ?? null,
            ]
        ))->map(fn ($row) => (array) $row);
    }

    public function active(): Collection
    {
        return collect(DB::select('CALL sp_materias_active()'))->map(fn ($row) => (array) $row);
    }

    public function create(array $data): array
    {
        DB::select(
            'CALL sp_materias_store(?, ?, ?, ?, ?)',
            [
                $data['idMateria'],
                $data['idCarrera'],
                $data['idMateriaPrevia'] ?? null,
                $data['nombre'],
                $data['semestre'],
            ]
        );

        return $this->findOrFail($data['idMateria']);
    }

    public function update(string $id, array $data): array
    {
        DB::select(
            'CALL sp_materias_update(?, ?, ?, ?, ?)',
            [
                $id,
                $data['idCarrera'],
                $data['idMateriaPrevia'] ?? null,
                $data['nombre'],
                $data['semestre'],
            ]
        );

        return $this->findOrFail($id);
    }

    public function disable(string $id): array
    {
        DB::select('CALL sp_materias_disable(?)', [$id]);

        return $this->findOrFail($id);
    }

    public function enable(string $id): void
    {
        DB::statement('UPDATE materias SET estado = 1, updated_at = NOW() WHERE idMateria = ?', [$id]);
    }

    public function findOrFail(string $id): array
    {
        $rows = DB::select('CALL sp_materias_find(?)', [$id]);

        if ($rows === []) {
            throw (new ModelNotFoundException())->setModel('Materia', [$id]);
        }

        return (array) $rows[0];
    }
}
