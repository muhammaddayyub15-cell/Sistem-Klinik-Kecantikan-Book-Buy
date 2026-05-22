<?php

namespace App\Service;

use App\Service\Repositories\MedicalRepository;
use App\Shared\Base\BaseService;

class MedicalService extends BaseService
{
    public function __construct(MedicalRepository $medicalRepository)
    {
        parent::__construct($medicalRepository);
    }
}
