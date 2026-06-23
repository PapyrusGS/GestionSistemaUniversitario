<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Devuelve todos los usuarios cargando el rol (Eager Loading) para evitar N+1.
     */
    public function index(): JsonResponse
    {
        $users = User::with('rol')->get();

        return response()->json([
            'users' => $users->map(function ($user) {
                return [
                    'idUsuario' => $user->idUsuario,
                    'nombreCompleto' => $user->nombreCompleto,
                    'correo' => $user->correo,
                    'rol' => $user->rol ? $user->rol->nombre : 'Sin rol',
                    'estado' => $user->estado,
                ];
            })
        ]);
    }

    /**
     * Registra un nuevo usuario encriptando su contraseña.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create($data);

        return response()->json([
            'message' => 'Usuario registrado correctamente.',
            'user' => [
                'idUsuario' => $user->idUsuario,
                'nombreCompleto' => $user->nombreCompleto,
                'correo' => $user->correo,
                'rol' => $user->rol()->first()->nombre ?? 'Sin rol',
                'estado' => $user->estado,
            ]
        ], 201);
    }

    /**
     * Lista los roles activos disponibles para asignación en los select del frontend.
     */
    public function roles(): JsonResponse
    {
        $roles = Rol::where('estado', true)->get(['idRol', 'nombre']);

        return response()->json([
            'roles' => $roles
        ]);
    }
}
