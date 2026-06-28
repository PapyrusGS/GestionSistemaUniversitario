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
                    'ci' => $user->ci,
                    'correo' => $user->correo,
                    'telefono' => $user->telefono,
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

        $data['fechaRegistro'] = now();
        $data['estado'] = true;
        $data['fechaA'] = now();
        $data['UsuarioA'] = auth()->id() ?? 1;
        $data['estadoA'] = true;

        $user = \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $user = User::create($data);

            $rol = Rol::find($data['idRol']);
            if ($rol && $rol->nombre === 'Estudiante') {
                $idEstudiante = \Illuminate\Support\Facades\DB::table('estudiante')->insertGetId([
                    'idUsuario' => $user->idUsuario,
                    'nombrePadre' => 'Sin Registrar',
                    'apellidoPadre' => 'Sin Registrar',
                    'nombreMadre' => null,
                    'apellidoMadre' => null,
                    'numeroPadre' => null,
                    'numeroMadre' => null,
                    'fechaA' => now(),
                    'UsuarioA' => auth()->id() ?? '1',
                    'estadoA' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                \Illuminate\Support\Facades\DB::table('estudiante_carrera')->insert([
                    'idEstudiante' => $idEstudiante,
                    'idCarrera' => $data['idCarrera'],
                    'fechaRegistro' => now(),
                    'estado' => true,
                    'fechaA' => now(),
                    'UsuarioA' => auth()->id() ?? '1',
                    'estadoA' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $user;
        });

        return response()->json([
            'message' => 'Usuario registrado correctamente.',
            'user' => [
                'idUsuario' => $user->idUsuario,
                'nombreCompleto' => $user->nombreCompleto,
                'ci' => $user->ci,
                'correo' => $user->correo,
                'telefono' => $user->telefono,
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
