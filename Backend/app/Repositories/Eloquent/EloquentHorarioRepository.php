<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\HorarioRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentHorarioRepository implements HorarioRepositoryInterface
{
    public function activeForSelect(): Collection
    {
        return collect(DB::select('CALL sp_horarios_active()'))->map(fn ($row) => (array) $row);
    }
}
