<?php

namespace App\Repositories\Eloquent;

use App\Models\Carrera;
use App\Repositories\Contracts\CarreraRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentCarreraRepository implements CarreraRepositoryInterface
{
    public function allOrdered(): Collection
    {
        return Carrera::query()
            ->orderByDesc('estado')
            ->orderBy('nombre')
            ->get();
    }

    public function create(array $data): Carrera
    {
        return Carrera::query()->create($data);
    }

    public function findOrFail(int $id): Carrera
    {
        return Carrera::query()->findOrFail($id);
    }
}
