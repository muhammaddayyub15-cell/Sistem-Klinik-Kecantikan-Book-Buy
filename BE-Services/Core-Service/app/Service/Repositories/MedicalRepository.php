<?php

namespace App\Service\Repositories;

use App\Models\Medical\MedicalRecord;
use App\Shared\Base\BaseRepository;

class MedicalRepository extends BaseRepository
{
    public function __construct(MedicalRecord $medicalRecord)
    {
        parent::__construct($medicalRecord);
    }
}
