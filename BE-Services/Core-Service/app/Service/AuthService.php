<?php

namespace App\Service;

use App\Service\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    // AuthService tidak extends BaseService karena flow auth (login/logout/token)

    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    // register: membuat user baru + hash password + generate token langsung
    public function register(array $data): array
    {
        $data['password'] = Hash::make($data['password']);

        $user  = $this->userRepository->create($data);
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    // login: validasi credential secara manual (stateless API, tidak pakai session)
    // Auth::attempt() tidak dipakai karena menggunakan session driver
    public function login(array $credentials): array
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        // pastikan akun aktif sebelum login
        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['Akun Anda tidak aktif. Hubungi administrator.'],
            ]);
        }

        // update last_login_at setiap login berhasil
        $this->userRepository->updateLastLogin($user->id);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    // logout: revoke hanya token yang sedang dipakai
    public function logout($user): void
    {
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
    }
}