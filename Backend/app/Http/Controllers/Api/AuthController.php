<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Support\ApiResponse;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->validated()['login'],
            $request->validated()['password']
        );

        return ApiResponse::success($result, 'Inicio de sesion exitoso.');
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(
            ['user' => $this->authService->me($request->user())],
            'Usuario autenticado.'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return ApiResponse::success(null, 'Sesion cerrada correctamente.');
    }
}
