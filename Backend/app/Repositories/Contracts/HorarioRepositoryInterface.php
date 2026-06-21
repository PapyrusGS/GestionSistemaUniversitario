<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface HorarioRepositoryInterface
{
    public function activeForSelect(): Collection;
}
