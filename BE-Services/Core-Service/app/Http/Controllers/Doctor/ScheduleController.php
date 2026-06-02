<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\StoreScheduleRequest;
use App\Http\Requests\Doctor\UpdateScheduleRequest;
use App\Service\ScheduleService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ScheduleController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected ScheduleService $scheduleService) {}

    // GET /doctors/{doctorId}/schedules
    // Ambil semua jadwal dokter — admin & dokter yang bersangkutan
    public function index(int $doctorId): JsonResponse
    {
        try {
            $schedules = $this->scheduleService->getByDoctor($doctorId);
            return $this->successResponse($schedules);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->errorResponse('Dokter tidak ditemukan', 404);
        } catch (\Exception) {
            return $this->errorResponse('Gagal mengambil jadwal dokter', 500);
        }
    }

    // GET /doctors/{doctorId}/schedules/active
    // Ambil jadwal aktif saja — publik (dipakai pasien saat booking)
    public function active(int $doctorId): JsonResponse
    {
        try {
            $schedules = $this->scheduleService->getActiveByDoctor($doctorId);
            return $this->successResponse($schedules);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->errorResponse('Dokter tidak ditemukan', 404);
        } catch (\Exception) {
            return $this->errorResponse('Gagal mengambil jadwal aktif dokter', 500);
        }
    }

    // POST /doctors/{doctorId}/schedules
    // Tambah jadwal baru — hanya admin
    public function store(StoreScheduleRequest $request, int $doctorId): JsonResponse
    {
        try {
            $schedule = $this->scheduleService->addSchedule($doctorId, $request->validated());
            return $this->successResponse($schedule, 'Jadwal berhasil ditambahkan', 201);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->errorResponse('Dokter tidak ditemukan', 404);
        } catch (\Exception) {
            return $this->errorResponse('Gagal menambahkan jadwal', 500);
        }
    }

    // PUT /doctors/{doctorId}/schedules/{scheduleId}
    // Update jadwal — hanya admin
    public function update(UpdateScheduleRequest $request, int $doctorId, int $scheduleId): JsonResponse
    {
        try {
            $schedule = $this->scheduleService->updateSchedule($doctorId, $scheduleId, $request->validated());
            return $this->successResponse($schedule, 'Jadwal berhasil diperbarui');
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->errorResponse('Jadwal tidak ditemukan', 404);
        } catch (\Exception) {
            return $this->errorResponse('Gagal memperbarui jadwal', 500);
        }
    }

    // DELETE /doctors/{doctorId}/schedules/{scheduleId}
    // Hapus jadwal — hanya admin
    public function destroy(int $doctorId, int $scheduleId): JsonResponse
    {
        try {
            $this->scheduleService->deleteSchedule($doctorId, $scheduleId);
            return $this->successResponse(null, 'Jadwal berhasil dihapus');
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->errorResponse('Jadwal tidak ditemukan', 404);
        } catch (\Exception) {
            return $this->errorResponse('Gagal menghapus jadwal', 500);
        }
    }

    // PATCH /doctors/{doctorId}/schedules/{scheduleId}/toggle
    // Toggle aktif/nonaktif jadwal — hanya admin
    public function toggle(int $doctorId, int $scheduleId): JsonResponse
    {
        try {
            $schedule = $this->scheduleService->toggleActive($doctorId, $scheduleId);
            return $this->successResponse($schedule, 'Status jadwal berhasil diubah');
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->errorResponse('Jadwal tidak ditemukan', 404);
        } catch (\Exception) {
            return $this->errorResponse('Gagal mengubah status jadwal', 500);
        }
    }
}