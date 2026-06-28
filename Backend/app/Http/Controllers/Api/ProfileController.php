<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\ProfileService;
use App\Services\AuthService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profileService,
        private readonly AuthService $authService,
    ) {
    }

    public function updatePassword(
        UpdatePasswordRequest $request
    ): JsonResponse {

        $this->profileService->updatePassword(
            $request->user(),
            $request->validated()['current_password'],
            $request->validated()['password'],
        );

        return ApiResponse::success(
            null,
            'Contraseña actualizada correctamente.'
        );
    }

    public function updateProfile(
        UpdateProfileRequest $request
    ): JsonResponse {

        $user = $this->profileService->updateProfile(
            $request->user(),
            $request->validated(),
        );

        return ApiResponse::success(
            $this->authService->me($user),
            'Perfil actualizado correctamente.'
        );
    }
}