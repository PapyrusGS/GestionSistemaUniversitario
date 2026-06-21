<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\DocenteRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentDocenteRepository implements DocenteRepositoryInterface
{
    public function activeForSelect(): Collection
    {
        return collect(DB::select('CALL sp_docentes_active()'))->map(fn ($row) => (array) $row);
    }
}
