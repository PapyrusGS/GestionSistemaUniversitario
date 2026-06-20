<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findByLogin(string $login): ?User
    {
        return User::query()
            ->with('rol')
            ->where(function ($query) use ($login): void {
                $query->where('correo', $login)
                    ->orWhere('ci', $login);
            })
            ->first();
    }

    public function create(array $data): User
    {
        return User::query()->create($data);
    }

    public function allWithRole(): Collection
    {
        return User::query()->with('rol')->get();
    }
}
