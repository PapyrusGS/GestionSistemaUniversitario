<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface DocenteRepositoryInterface
{
    public function activeForSelect(): Collection;

    public function disponiblesForSelect(int $idPeriodo, ?int $h1, ?int $h2, ?int $h3): Collection;
}
