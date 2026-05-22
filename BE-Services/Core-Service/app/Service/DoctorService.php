<?php

namespace App\Service;

use App\Service\Repositories\DoctorRepository;
use App\Shared\Base\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class DoctorService extends BaseService
{
    protected DoctorRepository $doctorRepository;

    public function __construct(DoctorRepository $doctorRepository)
    {
        parent::__construct($doctorRepository);
        $this->doctorRepository = $doctorRepository;
    }

    // Ambil semua dokter dengan relasi lengkap
    public function getAllWithRelations(): Collection
    {
        return $this->doctorRepository->allWithRelations();
    }

    // Buat profil dokter baru
    // Catatan: user_id harus belum terdaftar sebagai dokter (dicek di FormRequest)
    public function createDoctor(array $data): mixed
    {
        return $this->doctorRepository->create($data);
    }

    // Update profil dokter
    // Catatan: user_id tidak boleh diubah setelah profil dibuat
    public function updateDoctor(int $id, array $data): mixed
    {
        $doctor = $this->doctorRepository->findOrFail($id);
        return $this->doctorRepository->update($doctor->doctor_id, $data);
    }

    // Ambil dokter berdasarkan spesialisasi
    public function getBySpecialization(int $specId): Collection
    {
        return $this->doctorRepository->findBySpecialization($specId);
    }

    // Ambil semua dokter yang tersedia
    public function getAvailable(): Collection
    {
        return $this->doctorRepository->findAvailable();
    }

    // Ambil dokter beserta jadwalnya
    public function getWithSchedules(int $doctorId): mixed
    {
        $doctor = $this->doctorRepository->findWithSchedules($doctorId);

        if (!$doctor) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                'Dokter tidak ditemukan.'
            );
        }

        return $doctor;
    }

    // Toggle ketersediaan dokter (available / tidak)
    public function toggleAvailability(int $id): mixed
    {
        $doctor = $this->doctorRepository->findOrFail($id);

        return $this->doctorRepository->update($id, [
            'is_available' => !$doctor->is_available,
        ]);
    }
}