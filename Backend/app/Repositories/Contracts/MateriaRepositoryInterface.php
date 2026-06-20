<?php

namespace App\Repositories\Contracts;

use App\Models\Materia;
use Illuminate\Support\Collection;

interface MateriaRepositoryInterface
{
    public function allOrdered(): Collection;

    public function active(): Collection;

    public function create(array $data): array;

    public function update(string $id, array $data): array;

    public function disable(string $id): array;

    public function findOrFail(string $id): array;
}
