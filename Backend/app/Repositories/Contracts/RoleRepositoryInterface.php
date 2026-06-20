<?php

namespace App\Repositories\Contracts;

use App\Models\Rol;
use Illuminate\Support\Collection;

interface RoleRepositoryInterface
{
    public function active(): Collection;

    public function findByName(string $name): ?Rol;
}
