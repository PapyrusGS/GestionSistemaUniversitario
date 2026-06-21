<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface DocenteRepositoryInterface
{
    public function activeForSelect(): Collection;
}
