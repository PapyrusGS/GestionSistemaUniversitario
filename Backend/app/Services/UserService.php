<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly RoleRepositoryInterface $roles,
    ) {
    }

    public function roles(): Collection
    {
        return $this->roles->active();
    }

    public function index(): Collection
    {
        return $this->users->allWithRole()->map(fn (User $user) => $this->payload($user));
    }

    public function store(array $validated): array
    {
        $user = $this->users->create([
            'idRol' => $validated['idRol'],
            'nombre1' => $validated['nombre1'],
            'nombre2' => $validated['nombre2'] ?? null,
            'apellido1' => $validated['apellido1'],
            'apellido2' => $validated['apellido2'] ?? null,
            'ci' => $validated['ci'],
            'correo' => $validated['correo'],
            'password' => Hash::make($validated['password']),
            'fechaRegistro' => now(),
            'estado' => true,
        ]);

        return $this->payload($user->fresh('rol'));
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
            'ci' => $user->ci,
            'correo' => $user->correo,
            'estado' => (bool) $user->estado,
            'fechaRegistro' => $user->fechaRegistro?->format('Y-m-d H:i'),
        ];
    }
}
