<?php

namespace App\Service;

use App\Models\Patient\Patient;
use App\Service\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {

            $payload = [
                'full_name' => $data['full_name'],
                'email'     => $data['email'],
                'password'  => Hash::make($data['password']),
                'role'      => 'patient',
            ];

            $user = $this->userRepository->create($payload);

            Patient::create([
                'user_id'       => $user->user_id,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender'        => $data['gender'] ?? null,
                'blood_type'    => $data['blood_type'] ?? null,
                'address'       => $data['address'] ?? null,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user'  => $user,
                'token' => $token,
            ];
        });
    }

    public function login(array $credentials): array
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['Akun Anda tidak aktif. Hubungi administrator.'],
            ]);
        }

        $this->userRepository->updateLastLogin($user->user_id);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    public function logout($user): void
    {
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
    }

    // ── Rotating Token ────────────────────────────────────────────────────────
    // Hapus token lama, buat token baru. Dipanggil oleh AuthController@refresh.
    // Endpoint: POST /auth/refresh (harus pakai middleware auth:sanctum)
    public function refresh($user): array
    {
        // Hapus token yang sedang aktif
        $user->currentAccessToken()->delete();

        // Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }
}