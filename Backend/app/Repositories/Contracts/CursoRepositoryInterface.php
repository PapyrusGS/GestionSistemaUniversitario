<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface CursoRepositoryInterface
{
    public function allOrdered(): Collection;

    public function create(array $data): array;
}
