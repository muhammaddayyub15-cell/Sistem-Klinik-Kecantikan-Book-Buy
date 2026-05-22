<?php

namespace App\Modules\Auth\Services;

use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    // register: membuat user baru + hash password + set role default
    public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['role'] = $data['role'] ?? 'patient';

        return $this->userRepository->create($data);
    }

    // login: validasi credential + generate token untuk API
    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();

        // generate token (WAJIB untuk API / microservice)
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    // logout: revoke token user (bukan session logout)
    public function logout()
    {
        $user = Auth::user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
    }
}