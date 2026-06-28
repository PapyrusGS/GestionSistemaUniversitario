<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\ProfileRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class EloquentProfileRepository implements ProfileRepositoryInterface
{
    public function updatePassword(User $user, string $password): User
    {
        $user->password = Hash::make($password);
        $user->save();

        return $user;
    }

    public function updateProfile(User $user, array $data): User
    {
        $user->fill($data);
        $user->save();

        return $user;
    }
}