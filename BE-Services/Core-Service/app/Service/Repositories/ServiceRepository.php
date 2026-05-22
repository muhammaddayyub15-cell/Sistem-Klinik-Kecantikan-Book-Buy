<?php

namespace App\Service\Repositories;

use App\Models\Service\Service;
use App\Shared\Base\BaseRepository;

class ServiceRepository extends BaseRepository
{
    public function __construct(Service $service)
    {
        parent::__construct($service);
    }
}
