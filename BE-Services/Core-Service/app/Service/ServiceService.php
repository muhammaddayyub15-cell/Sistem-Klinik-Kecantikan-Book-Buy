<?php

namespace App\Service;

use App\Service\Repositories\ServiceRepository;
use App\Shared\Base\BaseService;

class ServiceService extends BaseService
{
    public function __construct(ServiceRepository $serviceRepository)
    {
        parent::__construct($serviceRepository);
    }
}
