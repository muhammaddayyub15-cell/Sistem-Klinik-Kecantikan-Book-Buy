<?php

namespace App\Service\Repositories;

use App\Models\Doctor\Doctor;
use App\Shared\Base\BaseRepository;

class DoctorRepository extends BaseRepository
{
    public function __construct(Doctor $doctor)
    {
        parent::__construct($doctor);
    }
}
