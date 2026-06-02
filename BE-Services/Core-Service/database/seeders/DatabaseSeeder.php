<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SpecializationSeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            DoctorSeeder::class,
            DoctorScheduleSeeder::class,
            PatientSeeder::class,
            BookingsSeeder::class,
            MedicalRecordsSeeder::class,
            PrescriptionsSeeder::class,
        ]);
    }
}