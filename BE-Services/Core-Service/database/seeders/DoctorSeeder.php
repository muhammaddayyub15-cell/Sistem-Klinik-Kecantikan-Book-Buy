<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Doctor\Doctor;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // user_id 3 → dr. Sari Indah  (Aesthetic Dermatology  = spec_id 1)
        // user_id 4 → dr. Budi Santoso (Laser & Skin Expert   = spec_id 2)
        DB::table('doctors')->insertOrIgnore([
            [
                'doctor_id'    => 1,
                'user_id'      => 3,   // dr. Sari Indah
                'spec_id'      => 1,   // Aesthetic Dermatology
                'license_no'   => 'STR-001-2024',
                'bio'          => 'Spesialis dermatologi estetik dengan pengalaman 10 tahun di bidang perawatan kulit dan kecantikan.',
                'is_active'    => true,
                'is_available' => true,
                'deleted_at'   => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'doctor_id'    => 2,
                'user_id'      => 4,   // dr. Budi Santoso
                'spec_id'      => 2,   // Laser & Skin Expert
                'license_no'   => 'STR-002-2024',
                'bio'          => 'Ahli terapi laser dan peremajaan kulit dengan sertifikasi internasional.',
                'is_active'    => true,
                'is_available' => true,
                'deleted_at'   => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ]);
    }
}