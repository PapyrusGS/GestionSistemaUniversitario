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

    public function disponiblesForSelect(int $idPeriodo, ?int $h1, ?int $h2, ?int $h3): Collection
    {
        if (!$h1) {
            return $this->activeForSelect();
        }

        return collect(DB::select('CALL sp_docentes_disponibles(?, ?, ?, ?)', [
            $idPeriodo,
            $h1,
            $h2,
            $h3
        ]))->map(fn ($row) => (array) $row);
    }
}
