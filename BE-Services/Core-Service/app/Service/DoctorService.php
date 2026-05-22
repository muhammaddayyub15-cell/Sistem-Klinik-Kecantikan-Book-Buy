<?php

namespace App\Service;

use App\Service\Repositories\DoctorRepository;
use App\Shared\Base\BaseService;

class DoctorService extends BaseService
{
    public function __construct(DoctorRepository $doctorRepository)
    {
        parent::__construct($doctorRepository);
    }
}
