<?php

namespace App\Service\Repositories;

use App\Models\Doctor\DoctorSchedule;
use App\Shared\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ScheduleRepository extends BaseRepository
{
    public function __construct(DoctorSchedule $schedule)
    {
        parent::__construct($schedule);
    }

    // Ambil semua jadwal milik satu dokter
    public function findByDoctor(int $doctorId): Collection
    {
        return $this->model
            ->where('doctor_id', $doctorId)
            ->orderByRaw("FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
            ->get();
    }

    // Ambil jadwal aktif milik satu dokter
    public function findActiveByDoctor(int $doctorId): Collection
    {
        return $this->model
            ->where('doctor_id', $doctorId)
            ->where('is_active', true)
            ->orderByRaw("FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
            ->get();
    }

    // Cek apakah jadwal baru overlap dengan jadwal lain pada hari & dokter yang sama
    // Dipakai saat create/update untuk mencegah jadwal bertabrakan
    public function hasTimeConflict(
        int    $doctorId,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?int   $excludeScheduleId = null  // diisi saat update agar tidak conflict dengan diri sendiri
    ): bool {
        $query = $this->model
            ->where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            // Overlap terjadi jika: start baru < end existing DAN end baru > start existing
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            });

        if ($excludeScheduleId) {
            $query->where('schedule_id', '!=', $excludeScheduleId);
        }

        return $query->exists();
    }

    // Cari jadwal dokter yang cocok dengan hari booking
    // Dipakai BookingService untuk validasi hari booking sesuai jadwal dokter
    public function findByDoctorAndDay(int $doctorId, string $dayOfWeek): ?DoctorSchedule
    {
        return $this->model
            ->where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();
    }
}