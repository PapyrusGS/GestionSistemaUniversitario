<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Get a listing of active roles.
     */
    public function roles(): JsonResponse
    {
        $roles = Rol::query()->where('estado', true)->get();

        return response()->json([
            'roles' => $roles,
        ]);
    }
    /**
     * Display a listing of the users with their roles.
     */
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->with('rol')
            ->get();

        $formattedUsers = $users->map(fn (User $user) => $this->payload($user));

        return response()->json([
            'users' => $formattedUsers,
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::query()->create([
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

        return response()->json([
            'message' => 'Usuario registrado correctamente.',
            'user' => $this->payload($user->fresh('rol')),
        ], 201);
    }

    /**
     * Format user response payload consistently.
     */
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
