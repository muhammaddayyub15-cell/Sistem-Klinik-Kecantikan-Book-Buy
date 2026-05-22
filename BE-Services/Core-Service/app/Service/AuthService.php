<?php

namespace App\Modules\Auth\Services;

use App\Modules\User\Repositories\UserRepository;
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
    public function register(array $data): array
    {
        // Hash password sebelum disimpan
        $data['password'] = Hash::make($data['password']);

        $user = $this->userRepository->create($data);

        // generate token langsung setelah register
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    // login: validasi credential secara manual (stateless API, tidak pakai session)
    public function login(array $credentials): array
    {
        // [FIX] Auth::attempt() menggunakan session driver — tidak cocok untuk stateless API
        // Ganti dengan manual lookup + Hash::check() agar kompatibel dengan Sanctum token
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

        // update last_login_at setiap kali login berhasil
        $this->userRepository->updateLastLogin($user->id);

        // generate token (revoke semua token lama opsional — aktifkan jika single session)
        // $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    // logout: revoke hanya token yang sedang dipakai (bukan semua token)
    public function logout($user): void
    {
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
    }
}