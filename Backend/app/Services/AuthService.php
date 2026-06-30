<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {
    }

    public function login(string $login, string $password): array
    {
        $user = $this->users->findByLogin($login);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Correo o contraseña incorrectos.'],
            ]);
        }

        if (! $user->estado) {
            throw ValidationException::withMessages([
                'login' => ['El usuario esta inactivo.'],
            ]);
        }

        return [
            'token' => $user->createToken('frontend')->plainTextToken,
            'user' => $this->payload($user->loadMissing('rol')),
        ];
    }

    public function me(User $user): array
    {
        return $this->payload($user->loadMissing('rol'));
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    private function payload(User $user): array
    {
        return [
            'idUsuario' => $user->idUsuario,
            'idRol' => $user->idRol,
            'rol' => $user->rol?->nombre,
            'nombreCompleto' => $user->nombre_completo,
            'nombre1' => $user->nombre1,
            'nombre2' => $user->nombre2,
            'apellido1' => $user->apellido1,
            'apellido2' => $user->apellido2,
            'ci'             => $user->ci,
            'correo'         => $user->correo,
            'telefono'       => $user->telefono,
            'estado'         => (bool) $user->estado,
            'fechaRegistro' => $user->fechaRegistro?->format('Y-m-d H:i'),
        ];
    }
}
