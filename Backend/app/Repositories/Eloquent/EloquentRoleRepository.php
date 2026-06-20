<?php

namespace App\Repositories\Eloquent;

use App\Models\Rol;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentRoleRepository implements RoleRepositoryInterface
{
    public function active(): Collection
    {
        return Rol::query()->where('estado', true)->get();
    }

    public function findByName(string $name): ?Rol
    {
        return Rol::query()->where('nombre', $name)->first();
    }
}
