<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface PeriodoRepositoryInterface
{
    public function activeForSelect(): Collection;
}
