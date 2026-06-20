<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\CarreraRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class EloquentCarreraRepository implements CarreraRepositoryInterface
{
    public function allOrdered(): Collection
    {
        return collect(DB::select('CALL sp_carreras_list()'))->map(fn ($row) => (array) $row);
    }

    public function activeForSelect(): Collection
    {
        return collect(DB::select('CALL sp_carreras_active()'))->map(fn ($row) => (array) $row);
    }

    public function create(array $data): array
    {
        $rows = DB::select('CALL sp_carreras_store(?, ?)', [
            $data['nombre'],
            $data['descripcion'] ?? null,
        ]);

        if ($rows === []) {
            throw (new ModelNotFoundException())->setModel('Carrera');
        }

        return (array) $rows[0];
    }

    public function update(int $id, array $data): array
    {
        DB::select('CALL sp_carreras_update(?, ?, ?)', [
            $id,
            $data['nombre'],
            $data['descripcion'] ?? null,
        ]);

        return $this->findOrFail($id);
    }

    public function destroy(int $id): array
    {
        DB::select('CALL sp_carreras_disable(?)', [$id]);

        return $this->findOrFail($id);
    }

    public function findOrFail(int $id): array
    {
        $rows = DB::select('CALL sp_carreras_find(?)', [$id]);

        if ($rows === []) {
            throw (new ModelNotFoundException())->setModel('Carrera', [$id]);
        }

        return (array) $rows[0];
    }
}
