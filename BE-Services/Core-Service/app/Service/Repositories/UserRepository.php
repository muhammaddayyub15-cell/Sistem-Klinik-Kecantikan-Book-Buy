<?php

namespace App\Service\Repositories;

use App\Models\User\User;
use App\Shared\Base\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct(new User());
    }

    // cari user berdasarkan email (dipakai AuthService::login)
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    // sudah ada di BaseRepository via find($id)
    // method ini di-override agar return type explicit
    public function findById(int $id): ?User
    {
        return $this->model->find($id);
    }

    // update kolom last_login_at setiap login berhasil
    public function updateLastLogin(int $userId): void
    {
        $this->model->where('id', $userId)->update([
            'last_login_at' => now(),
        ]);
    }
}