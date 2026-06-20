<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Support\ApiResponse;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    public function roles(): JsonResponse
    {
        return ApiResponse::success(
            ['roles' => $this->userService->roles()],
            'Roles cargados correctamente.'
        );
    }

    public function index(): JsonResponse
    {
        return ApiResponse::success(
            ['users' => $this->userService->index()],
            'Usuarios cargados correctamente.'
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->store($request->validated());

        return ApiResponse::success(
            ['user' => $user],
            'Usuario registrado correctamente.',
            201
        );
    }
}
