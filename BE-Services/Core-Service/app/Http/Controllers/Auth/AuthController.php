<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Service\AuthService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // register: endpoint publik untuk pendaftaran user baru
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return $this->successResponse(
            data: $result,
            message: 'Registrasi berhasil.',
            statusCode: Response::HTTP_CREATED
        );
    }

    // login: endpoint publik untuk autentikasi
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return $this->successResponse(
            data: $result,
            message: 'Login berhasil.'
        );
    }

    // logout: revoke token yang sedang dipakai (butuh auth:sanctum middleware)
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->successResponse(message: 'Logout berhasil.');
    }

    // me: kembalikan data user yang sedang login
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(data: $request->user());
    }
}