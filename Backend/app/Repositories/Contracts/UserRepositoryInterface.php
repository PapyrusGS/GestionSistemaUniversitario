<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function findByLogin(string $login): ?User;

    public function create(array $data): User;

    public function allWithRole(): Collection;
}
