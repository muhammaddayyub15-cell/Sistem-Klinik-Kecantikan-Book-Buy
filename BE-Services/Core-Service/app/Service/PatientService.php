<?php

namespace App\Service;

use App\Service\Repositories\PatientRepository;
use App\Shared\Base\BaseService;

class PatientService extends BaseService
{
    public function __construct(PatientRepository $patientRepository)
    {
        parent::__construct($patientRepository);
    }
}