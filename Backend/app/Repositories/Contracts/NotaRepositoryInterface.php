<?php

namespace App\Repositories\Contracts;

interface NotaRepositoryInterface
{
    public function create(array $data): array;

    public function updateNota(int $docenteId, int $notaId, float $nota): array;
}
