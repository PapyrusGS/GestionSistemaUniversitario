<?php

namespace App\Repositories\Contracts;

use App\Models\Carrera;
use Illuminate\Support\Collection;

interface CarreraRepositoryInterface
{
    public function allOrdered(): Collection;

    public function create(array $data): Carrera;

    public function findOrFail(int $id): Carrera;
}
