<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\ProfileRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileService
{
    public function __construct(
        private readonly ProfileRepositoryInterface $profiles,
    ) {
    }

    public function updatePassword(
        User $user,
        string $currentPassword,
        string $newPassword
    ): void {

        if (! Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => [
                    'La contraseña actual es incorrecta.'
                ]
            ]);
        }

        $this->profiles->updatePassword($user, $newPassword);
    }

    public function updateProfile(User $user, array $data): User
    {
        return $this->profiles->updateProfile($user, $data);
    }
}