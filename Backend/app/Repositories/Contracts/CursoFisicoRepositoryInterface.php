<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface CursoFisicoRepositoryInterface
{
    public function allOrdered(array $filters = []): Collection;

    public function active(): Collection;

    public function create(array $data): array;

    public function update(string $id, array $data): array;

    public function disable(string $id): array;

    public function enable(string $id): array;

    public function findOrFail(string $id): array;
}
