<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Services\AuthService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // register: membuat user baru
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string|in:admin,doctor,patient,staff',
        ]);

        $user = $this->authService->register($validated);

        return $this->successResponse($user, 'User registered successfully', 201);
    }

    // login: autentikasi user dan generate token
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = $this->authService->login($validated);

        return $this->successResponse($user, 'Login successful');
    }

    // logout: menghapus token user
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->successResponse(null, 'Logged out successfully');
    }

    // me: mengambil data user yang sedang login
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse($request->user());
    }
}