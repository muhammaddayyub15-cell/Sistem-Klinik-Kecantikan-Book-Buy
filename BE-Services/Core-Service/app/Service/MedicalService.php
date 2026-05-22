<?php

namespace App\Service;

use App\Service\Repositories\MedicalRepository;
use App\Shared\Base\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MedicalService extends BaseService
{
    protected MedicalRepository $medicalRepository;

    public function __construct(MedicalRepository $medicalRepository)
    {
        parent::__construct($medicalRepository);
        $this->medicalRepository = $medicalRepository;
    }

    // Ambil semua rekam medis dengan relasi lengkap
    public function getAllWithRelations(): Collection
    {
        return $this->medicalRepository->allWithRelations();
    }

    // Buat rekam medis baru
    // Catatan: satu booking hanya boleh punya satu rekam medis (dicek di FormRequest)
    public function createRecord(array $data): mixed
    {
        $data['recorded_at'] = now();
        return $this->medicalRepository->create($data);
    }

    // Tambah resep ke rekam medis yang sudah ada
    // Catatan: menggunakan DB transaction agar resep tersimpan semua atau tidak sama sekali
    public function addPrescriptions(int $recordId, array $prescriptions): mixed
    {
        // Pastikan rekam medis ada sebelum menyimpan resep
        $record = $this->medicalRepository->findOrFail($recordId);

        return DB::transaction(function () use ($record, $prescriptions) {
            $this->medicalRepository->storePrescriptions($record->record_id, $prescriptions);
            // Return rekam medis beserta resep terbaru
            return $this->medicalRepository->findOrFail($record->record_id);
        });
    }

    // Ganti semua resep pada rekam medis (hapus lama, simpan baru)
    // Catatan: digunakan jika dokter ingin merevisi seluruh resep
    public function replacePrescriptions(int $recordId, array $prescriptions): mixed
    {
        $record = $this->medicalRepository->findOrFail($recordId);

        return DB::transaction(function () use ($record, $prescriptions) {
            // Hapus resep lama terlebih dahulu
            $this->medicalRepository->deletePrescriptionsByRecord($record->record_id);
            // Simpan resep baru
            $this->medicalRepository->storePrescriptions($record->record_id, $prescriptions);
            return $this->medicalRepository->findOrFail($record->record_id);
        });
    }

    // Ambil rekam medis berdasarkan pasien
    public function getByPatient(int $patientId): Collection
    {
        return $this->medicalRepository->findByPatient($patientId);
    }

    // Ambil rekam medis berdasarkan dokter
    public function getByDoctor(int $doctorId): Collection
    {
        return $this->medicalRepository->findByDoctor($doctorId);
    }

    // Ambil rekam medis berdasarkan booking
    public function getByBooking(int $bookingId): mixed
    {
        $record = $this->medicalRepository->findByBooking($bookingId);

        if (!$record) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                'Rekam medis untuk booking ini tidak ditemukan.'
            );
        }

        return $record;
    }

    // Update rekam medis — hanya field diagnosis dan prescription_text yang boleh diubah
    // Catatan: booking_id, patient_id, doctor_id tidak boleh diubah setelah rekam medis dibuat
    public function updateRecord(int $id, array $data): mixed
    {
        $allowedFields = ['diagnosis', 'prescription_text'];
        $filteredData  = array_intersect_key($data, array_flip($allowedFields));

        return $this->medicalRepository->update($id, $filteredData);
    }
}