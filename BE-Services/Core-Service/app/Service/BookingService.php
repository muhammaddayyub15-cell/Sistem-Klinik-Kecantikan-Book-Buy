<?php

namespace App\Service;

use App\Service\Repositories\BookingRepository;
use App\Shared\Base\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class BookingService extends BaseService
{
    protected BookingRepository $bookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
        parent::__construct($bookingRepository);
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * Mendapatkan semua booking beserta relasi terkait (patient, doctor, doctorSchedule, service).
     */
    public function getAllWithRelations($user): Collection
{
    return match($user->role) {
        'patient' => $user->patient
            ? $this->bookingRepository->findByPatient($user->patient->patient_id)
            : collect(),
        'doctor'  => $user->doctor
            ? $this->bookingRepository->findByDoctor($user->doctor->doctor_id)
            : collect(),
        default   => $this->bookingRepository->allWithRelations(), // admin
    };
}

    /**
     * Membuat booking baru dengan validasi slot jadwal dokter.
     *
     * @throws ValidationException
     */
    public function createBooking(array $data): mixed
    {
        $slotTaken = $this->bookingRepository->isSlotTaken(
            $data['doctsched_id'],
            $data['booked_date']
        );

        if ($slotTaken) {
            throw ValidationException::withMessages([
                'doctsched_id' => 'This schedule slot is already booked for the selected date.',
            ]);
        }

        return $this->bookingRepository->create($data);
    }

    /**
     * Mengupdate status booking dengan validasi status saat ini (guard untuk completed/cancelled).
     *
     * @throws ValidationException
     */
    public function updateStatus(int $id, array $data): mixed
    {
        $booking = $this->bookingRepository->findOrFail($id);

        // Guard: tidak boleh update booking yang sudah completed atau cancelled
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            throw ValidationException::withMessages([
                'status' => "Cannot update a booking that is already {$booking->status}.",
            ]);
        }

        return $this->bookingRepository->update($id, $data);
    }
}