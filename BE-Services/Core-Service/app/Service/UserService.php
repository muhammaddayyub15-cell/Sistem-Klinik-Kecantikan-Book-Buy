<?php

namespace App\Service;

use App\Service\Repositories\UserRepository;
use App\Shared\Base\BaseService;

class UserService extends BaseService
{
    public function __construct(UserRepository $userRepository)
    {
        parent::__construct($userRepository);
    }
}
