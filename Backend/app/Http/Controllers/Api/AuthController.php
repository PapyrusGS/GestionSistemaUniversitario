<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre1' => ['required', 'string', 'max:255'],
            'nombre2' => ['nullable', 'string', 'max:255'],
            'apellido1' => ['required', 'string', 'max:255'],
            'apellido2' => ['nullable', 'string', 'max:255'],
            'ci' => ['required', 'string', 'max:255', 'unique:usuarios,ci'],
            'correo' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:usuarios,correo'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $rolEstudiante = Rol::query()
            ->where('nombre', 'Estudiante')
            ->firstOrFail();

        $user = User::query()->create([
            'idRol' => $rolEstudiante->idRol,
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

        $token = $user->createToken('frontend')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado correctamente.',
            'token' => $token,
            'user' => $this->payload($user->fresh('rol')),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->with('rol')
            ->where(function ($query) use ($credentials): void {
                $query->where('correo', $credentials['login'])
                    ->orWhere('ci', $credentials['login']);
            })
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Las credenciales no son válidas.'],
            ]);
        }

        if (! $user->estado) {
            throw ValidationException::withMessages([
                'login' => ['El usuario está inactivo.'],
            ]);
        }

        $token = $user->createToken('frontend')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'token' => $token,
            'user' => $this->payload($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('rol');

        return response()->json([
            'user' => $this->payload($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
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
