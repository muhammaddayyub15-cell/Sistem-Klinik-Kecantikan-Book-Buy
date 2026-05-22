<?php

namespace App\Service\Repositories;

use App\Models\Booking\Booking;
use App\Shared\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class BookingRepository extends BaseRepository
{
    public function __construct(Booking $booking)
    {
        parent::__construct($booking);
    }

    /**
     * Mendapatkan semua booking beserta relasi terkait (patient, doctor, doctorSchedule, service).
     */
    public function allWithRelations(): Collection
    {
        return $this->model
            ->with(['patient', 'doctor', 'doctorSchedule', 'service'])
            ->latest('booked_date')
            ->get();
    }

    /**
     * Mendapatkan booking berdasarkan patient_id beserta relasi terkait (doctor, doctorSchedule, service).
     */
    public function findByPatient(int $patientId): Collection
    {
        return $this->model
            ->with(['doctor', 'doctorSchedule', 'service'])
            ->where('patient_id', $patientId)
            ->latest('booked_date')
            ->get();
    }

    /**
     * Mendapatkan booking berdasarkan doctor_id beserta relasi terkait (patient, doctorSchedule, service).
     */
    public function findByDoctor(int $doctorId): Collection
    {
        return $this->model
            ->with(['patient', 'doctorSchedule', 'service'])
            ->where('doctor_id', $doctorId)
            ->latest('booked_date')
            ->get();
    }

    /**
     * Cek apakah slot booking sudah terisi untuk jadwal dokter tertentu pada tanggal tertentu.
     */
    public function isSlotTaken(int $doctorSchedId, string $bookedDate, ?int $excludeBookingId = null): bool
    {
        return $this->model
            ->where('doctsched_id', $doctorSchedId)
            ->where('booked_date', $bookedDate)
            ->whereNotIn('status', ['cancelled'])
            ->when($excludeBookingId, fn($q) => $q->where('booking_id', '!=', $excludeBookingId))
            ->exists();
    }
}