<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\ProfileRepositoryInterface;
use Illuminate\Support\Facades\DB;
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
        string $newPassword,
        string $ip
    ): void {

        if (! Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => [
                    'La contraseña actual es incorrecta.'
                ]
            ]);
        }

        $this->profiles->updatePassword($user, $newPassword);

        DB::table('auditorias')->insert([
            'tabla_nombre' => 'usuarios',
            'registro_id' => $user->idUsuario,
            'accion' => 'U',
            'campo' => 'password',
            'valor_anterior' => null,
            'valor_nuevo' => null,
            'usuario_a' => $user->idUsuario,
            'fecha_a' => now(),
            'direccion_ip' => $ip,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function updateProfile(User $user, array $data, string $ip): User
    {
        $original = $user->only(array_keys($data));
        $updated = $this->profiles->updateProfile($user, $data);

        foreach ($data as $field => $newValue) {
            $oldValue = $original[$field] ?? null;
            if ($oldValue !== $newValue) {
                DB::table('auditorias')->insert([
                    'tabla_nombre' => 'usuarios',
                    'registro_id' => $user->idUsuario,
                    'accion' => 'U',
                    'campo' => $field,
                    'valor_anterior' => $oldValue !== null ? (string) $oldValue : null,
                    'valor_nuevo' => $newValue !== null ? (string) $newValue : null,
                    'usuario_a' => $user->idUsuario,
                    'fecha_a' => now(),
                    'direccion_ip' => $ip,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return $updated;
    }
}