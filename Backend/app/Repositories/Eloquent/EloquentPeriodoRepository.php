<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\PeriodoRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentPeriodoRepository implements PeriodoRepositoryInterface
{
    public function activeForSelect(): Collection
    {
        return collect(DB::select('CALL sp_periodos_active()'))->map(fn ($row) => (array) $row);
    }
}
