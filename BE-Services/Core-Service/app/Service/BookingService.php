<?php

namespace App\Service;

use App\Service\Repositories\BookingRepository;
use App\Service\Repositories\ScheduleRepository;
use App\Shared\Base\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class BookingService extends BaseService
{
    public function __construct(
        protected BookingRepository  $bookingRepository,
        protected ScheduleRepository $scheduleRepository,
    ) {
        parent::__construct($bookingRepository);
    }

    // Mendapatkan semua booking berdasarkan role user
    public function getAllWithRelations($user): Collection
    {
        return match ($user->role) {
            'patient' => $user->patient
                ? $this->bookingRepository->findByPatient($user->patient->patient_id)
                : collect(),
            'doctor'  => $user->doctor
                ? $this->bookingRepository->findByDoctor($user->doctor->doctor_id)
                : collect(),
            default   => $this->bookingRepository->allWithRelations(), // admin
        };
    }

    // Membuat booking baru dengan validasi hari dan slot jadwal dokter
    public function createBooking(array $data): mixed
    {
        // Ambil patient_id dari user yang sedang login
        $user    = auth()->user()->load('patient');
        $patient = $user->patient;

        if (!$patient) {
            throw ValidationException::withMessages([
                'patient_id' => 'Patient profile not found for this user.',
            ]);
        }

        // Override patient_id dari token — jangan percaya dari request
        $data['patient_id'] = $patient->patient_id;

        $bookedDate = $data['booked_date'];
        $doctorId   = $data['doctor_id'];

        // Dapatkan nama hari dari tanggal booking (e.g. "Monday")
        $dayOfWeek = \Carbon\Carbon::parse($bookedDate)->format('l');

        // Validasi 1: dokter harus punya jadwal aktif di hari tersebut
        $schedule = $this->scheduleRepository->findByDoctorAndDay($doctorId, $dayOfWeek);

        if (!$schedule) {
            throw ValidationException::withMessages([
                'booked_date' => "Doctor does not have an active schedule on {$dayOfWeek}.",
            ]);
        }

        // Set data dari schedule yang ditemukan
        $data['doctor_schedule_id'] = $schedule->schedule_id;
        $data['start_time']         = $schedule->start_time;
        $data['end_time']           = $schedule->end_time;

        // Validasi 2: slot pada hari tersebut belum diambil
        $slotTaken = $this->bookingRepository->isSlotTaken(
            $data['doctor_schedule_id'],
            $bookedDate,
        );

        if ($slotTaken) {
            throw ValidationException::withMessages([
                'booked_date' => 'This schedule slot is already booked for the selected date.',
            ]);
        }

        return $this->bookingRepository->create($data);
    }

    // Mengupdate status booking dengan guard untuk completed/cancelled
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