<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Service\AuthService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return $this->successResponse(
            data: $result,
            message: 'Registrasi berhasil.',
            code: Response::HTTP_CREATED
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return $this->successResponse(
            data: $result,
            message: 'Login berhasil.'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->successResponse(
            data: null,
            message: 'Logout berhasil.'
        );
    }

    public function me(Request $request): JsonResponse
{
    $user = $request->user()->load('patient');
    return $this->successResponse(
        data: array_merge($user->toArray(), [
            'patient_id' => $user->patient?->patient_id,
        ])
    );
    }

    public function validateToken(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->successResponse(
            data: [
                'user_id' => $user->user_id,
                'email'   => $user->email,
                'role'    => $user->role,
                'name'    => $user->full_name,
            ],
            message: 'Token valid.'
        );
    }

    public function refresh(Request $request): JsonResponse
    {
        $result = $this->authService->refresh($request->user());

        return $this->successResponse(
            data: $result,
            message: 'Token diperbarui.'
        );
    }
}