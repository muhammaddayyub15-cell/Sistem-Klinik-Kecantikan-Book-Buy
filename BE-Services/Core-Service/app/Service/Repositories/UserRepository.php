<?php

namespace App\Service\Repositories;

use App\Models\User\User;
use App\Shared\Base\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }
}
