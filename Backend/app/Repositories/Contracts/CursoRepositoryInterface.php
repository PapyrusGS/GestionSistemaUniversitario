<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface CursoRepositoryInterface
{
    public function allOrdered(): Collection;

    public function activePhysicalForSelect(): Collection;

    public function create(array $data): array;

    public function disable($id): array;

    public function enable($id): array;
}
