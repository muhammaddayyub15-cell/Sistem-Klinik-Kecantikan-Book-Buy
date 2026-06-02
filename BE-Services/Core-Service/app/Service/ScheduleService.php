<?php

namespace App\Service;

use App\Service\Repositories\DoctorRepository;
use App\Service\Repositories\ScheduleRepository;
use App\Shared\Base\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class ScheduleService extends BaseService
{
    public function __construct(
        protected ScheduleRepository $scheduleRepository,
        protected DoctorRepository   $doctorRepository,
    ) {
        parent::__construct($scheduleRepository);
    }

    // Ambil semua jadwal milik dokter (validasi dokter exist dulu)
    public function getByDoctor(int $doctorId): Collection
    {
        $this->doctorRepository->findOrFail($doctorId);
        return $this->scheduleRepository->findByDoctor($doctorId);
    }

    // Ambil jadwal aktif milik dokter
    public function getActiveByDoctor(int $doctorId): Collection
    {
        $this->doctorRepository->findOrFail($doctorId);
        return $this->scheduleRepository->findActiveByDoctor($doctorId);
    }

    // Tambah jadwal baru untuk dokter
    // Validasi: overlap waktu di hari yang sama dicegah
    public function addSchedule(int $doctorId, array $data): mixed
    {
        $this->doctorRepository->findOrFail($doctorId);

        $this->validateNoConflict(
            doctorId:  $doctorId,
            dayOfWeek: $data['day_of_week'],
            startTime: $data['start_time'],
            endTime:   $data['end_time'],
        );

        return $this->scheduleRepository->create([
            ...$data,
            'doctor_id' => $doctorId,
        ]);
    }

    // Update jadwal — validasi konflik dengan exclude ID jadwal yang sedang diedit
    public function updateSchedule(int $doctorId, int $scheduleId, array $data): mixed
    {
        $schedule = $this->scheduleRepository->findOrFail($scheduleId);

        // Pastikan jadwal ini memang milik dokter yang bersangkutan
        if ($schedule->doctor_id !== $doctorId) {
            throw ValidationException::withMessages([
                'schedule_id' => 'Schedule does not belong to this doctor.',
            ]);
        }

        // Hanya validasi konflik waktu jika ada perubahan hari/waktu
        $dayOfWeek = $data['day_of_week'] ?? $schedule->day_of_week;
        $startTime = $data['start_time']  ?? $schedule->start_time;
        $endTime   = $data['end_time']    ?? $schedule->end_time;

        $this->validateNoConflict(
            doctorId:          $doctorId,
            dayOfWeek:         $dayOfWeek,
            startTime:         $startTime,
            endTime:           $endTime,
            excludeScheduleId: $scheduleId,
        );

        return $this->scheduleRepository->update($scheduleId, $data);
    }

    // Hapus jadwal
    public function deleteSchedule(int $doctorId, int $scheduleId): void
    {
        $schedule = $this->scheduleRepository->findOrFail($scheduleId);

        if ($schedule->doctor_id !== $doctorId) {
            throw ValidationException::withMessages([
                'schedule_id' => 'Schedule does not belong to this doctor.',
            ]);
        }

        $this->scheduleRepository->delete($scheduleId);
    }

    // Toggle aktif/nonaktif jadwal
    public function toggleActive(int $doctorId, int $scheduleId): mixed
    {
        $schedule = $this->scheduleRepository->findOrFail($scheduleId);

        if ($schedule->doctor_id !== $doctorId) {
            throw ValidationException::withMessages([
                'schedule_id' => 'Schedule does not belong to this doctor.',
            ]);
        }

        return $this->scheduleRepository->update($scheduleId, [
            'is_active' => !$schedule->is_active,
        ]);
    }

    // Helper — throw ValidationException jika ada konflik waktu
    private function validateNoConflict(
        int    $doctorId,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?int   $excludeScheduleId = null,
    ): void {
        if ($startTime >= $endTime) {
            throw ValidationException::withMessages([
                'end_time' => 'End time must be after start time.',
            ]);
        }

        $conflict = $this->scheduleRepository->hasTimeConflict(
            doctorId:          $doctorId,
            dayOfWeek:         $dayOfWeek,
            startTime:         $startTime,
            endTime:           $endTime,
            excludeScheduleId: $excludeScheduleId,
        );

        if ($conflict) {
            throw ValidationException::withMessages([
                'day_of_week' => "Doctor already has an active schedule that overlaps on {$dayOfWeek} at the given time.",
            ]);
        }
    }
}