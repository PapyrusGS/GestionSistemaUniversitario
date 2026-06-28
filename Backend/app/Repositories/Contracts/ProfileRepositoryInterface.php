<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface ProfileRepositoryInterface
{
    public function updatePassword(User $user, string $password): User;

    public function updateProfile(User $user, array $data): User;
}