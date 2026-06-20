<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface CarreraRepositoryInterface
{
    public function allOrdered(): Collection;

    public function activeForSelect(): Collection;

    public function create(array $data): array;

    public function update(int $id, array $data): array;

    public function destroy(int $id): array;

    public function findOrFail(int $id): array;
}
