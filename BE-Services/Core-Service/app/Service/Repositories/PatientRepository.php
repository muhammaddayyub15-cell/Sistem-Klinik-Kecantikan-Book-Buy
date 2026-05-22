<?php

namespace App\Service\Repositories;

use App\Models\Patient\Patient;
use App\Shared\Base\BaseRepository;

class PatientRepository extends BaseRepository
{
    public function __construct(Patient $patient)
    {
        parent::__construct($patient);
    }
}
