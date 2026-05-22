<?php

namespace App\Service\Repositories;

use App\Models\Medical\MedicalRecord;
use App\Models\Medical\Prescription;
use App\Shared\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class MedicalRepository extends BaseRepository
{
    public function __construct(
        MedicalRecord $medicalRecord,
        // Inject Prescription model untuk operasi resep
        protected Prescription $prescriptionModel
    ) {
        parent::__construct($medicalRecord);
    }

    // Ambil semua rekam medis dengan relasi lengkap
    public function allWithRelations(): Collection
    {
        return $this->model
            ->with(['booking', 'patient', 'doctor', 'prescriptions'])
            ->latest('recorded_at')
            ->get();
    }

    // Ambil rekam medis berdasarkan pasien
    public function findByPatient(int $patientId): Collection
    {
        return $this->model
            ->with(['booking', 'doctor', 'prescriptions'])
            ->where('patient_id', $patientId)
            ->latest('recorded_at')
            ->get();
    }

    // Ambil rekam medis berdasarkan dokter
    public function findByDoctor(int $doctorId): Collection
    {
        return $this->model
            ->with(['booking', 'patient', 'prescriptions'])
            ->where('doctor_id', $doctorId)
            ->latest('recorded_at')
            ->get();
    }

    // Ambil rekam medis berdasarkan booking — relasi 1:1
    public function findByBooking(int $bookingId): ?MedicalRecord
    {
        return $this->model
            ->with(['patient', 'doctor', 'prescriptions'])
            ->where('booking_id', $bookingId)
            ->first();
    }

    // Simpan banyak resep sekaligus dalam satu transaksi
    public function storePrescriptions(int $recordId, array $prescriptions): bool
    {
        $data = array_map(function ($item) use ($recordId) {
            return [
                'record_id'              => $recordId,
                'product_id'             => $item['product_id'],
                // Snapshot nama produk saat resep dibuat — tidak ikut berubah jika produk diedit
                'product_name_snapshot'  => $item['product_name'],
                'qty'                    => $item['qty'],
                'dosage_instruction'     => $item['dosage_instruction'],
                'prescribed_at'          => now(),
                'created_at'             => now(),
                'updated_at'             => now(),
            ];
        }, $prescriptions);

        return $this->prescriptionModel->insert($data);
    }

    // Hapus semua resep berdasarkan record_id
    public function deletePrescriptionsByRecord(int $recordId): void
    {
        $this->prescriptionModel
            ->where('record_id', $recordId)
            ->delete();
    }
}