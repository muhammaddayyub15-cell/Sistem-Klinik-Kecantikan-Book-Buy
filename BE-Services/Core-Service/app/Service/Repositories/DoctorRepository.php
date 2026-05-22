<?php

namespace App\Service\Repositories;

use App\Models\Doctor\Doctor;
use App\Shared\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class DoctorRepository extends BaseRepository
{
    public function __construct(Doctor $doctor)
    {
        parent::__construct($doctor);
    }

    // Ambil semua dokter beserta relasi user dan spesialisasi
    public function allWithRelations(): Collection
    {
        return $this->model
            ->with(['user', 'specialization', 'schedules'])
            ->get();
    }

    // Ambil dokter berdasarkan spesialisasi
    public function findBySpecialization(int $specId): Collection
    {
        return $this->model
            ->with(['user', 'specialization', 'schedules'])
            ->where('spec_id', $specId)
            ->where('is_available', true)
            ->get();
    }

    // Ambil dokter yang tersedia (is_available = true)
    public function findAvailable(): Collection
    {
        return $this->model
            ->with(['user', 'specialization', 'schedules'])
            ->where('is_available', true)
            ->get();
    }

    // Ambil dokter beserta jadwal aktifnya
    public function findWithSchedules(int $doctorId): ?Doctor
    {
        return $this->model
            ->with(['user', 'specialization', 'schedules'])
            ->where('doctor_id', $doctorId)
            ->first();
    }
}