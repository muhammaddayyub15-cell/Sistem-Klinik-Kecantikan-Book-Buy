<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\StoreDoctorRequest;
use App\Http\Requests\Doctor\UpdateDoctorRequest;
use App\Service\DoctorService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class DoctorController extends Controller
{
    use ApiResponseTrait;

    // Inject DoctorService melalui constructor
    public function __construct(protected DoctorService $doctorService) {}

    // Menampilkan semua data dokter beserta relasi
    // Akses: semua role (publik untuk lihat daftar dokter)
    public function index(): JsonResponse
    {
        try {
            $doctors = $this->doctorService->getAllWithRelations();
            return $this->successResponse($doctors);
        } catch (\Exception $e) {
            // Gagal mengambil data dokter
            return $this->errorResponse('Gagal mengambil data dokter', 500);
        }
    }

    // Membuat profil dokter baru
    // Akses: admin saja
    public function store(StoreDoctorRequest $request): JsonResponse
    {
        try {
            $doctor = $this->doctorService->createDoctor($request->validated());
            return $this->successResponse($doctor, 'Profil dokter berhasil dibuat', 201);
        } catch (\Exception $e) {
            // Gagal menyimpan profil dokter
            return $this->errorResponse('Gagal membuat profil dokter', 500);
        }
    }

    // Menampilkan detail satu dokter beserta jadwalnya
    // Akses: semua role
    public function show(int $id): JsonResponse
    {
        try {
            $doctor = $this->doctorService->getWithSchedules($id);
            return $this->successResponse($doctor);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Dokter dengan ID yang diberikan tidak ditemukan
            return $this->errorResponse('Dokter tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Gagal mengambil detail dokter
            return $this->errorResponse('Gagal mengambil detail dokter', 500);
        }
    }

    // Mengubah data profil dokter
    // Akses: admin saja
    public function update(UpdateDoctorRequest $request, int $id): JsonResponse
    {
        try {
            $doctor = $this->doctorService->updateDoctor($id, $request->validated());
            return $this->successResponse($doctor, 'Profil dokter berhasil diperbarui');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Dokter dengan ID yang diberikan tidak ditemukan
            return $this->errorResponse('Dokter tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Gagal memperbarui profil dokter
            return $this->errorResponse('Gagal memperbarui profil dokter', 500);
        }
    }

    // Menampilkan dokter berdasarkan spesialisasi
    // Akses: semua role
    public function getBySpecialization(int $specId): JsonResponse
    {
        try {
            $doctors = $this->doctorService->getBySpecialization($specId);
            return $this->successResponse($doctors);
        } catch (\Exception $e) {
            // Gagal mengambil dokter berdasarkan spesialisasi
            return $this->errorResponse('Gagal mengambil data dokter', 500);
        }
    }

    // Menampilkan dokter yang sedang tersedia
    // Akses: semua role
    public function getAvailable(): JsonResponse
    {
        try {
            $doctors = $this->doctorService->getAvailable();
            return $this->successResponse($doctors);
        } catch (\Exception $e) {
            // Gagal mengambil daftar dokter yang tersedia
            return $this->errorResponse('Gagal mengambil daftar dokter tersedia', 500);
        }
    }

    // Toggle ketersediaan dokter (aktif / tidak aktif)
    // Akses: admin saja
    public function toggleAvailability(int $id): JsonResponse
    {
        try {
            $doctor = $this->doctorService->toggleAvailability($id);
            return $this->successResponse($doctor, 'Status ketersediaan dokter berhasil diubah');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Dokter dengan ID yang diberikan tidak ditemukan
            return $this->errorResponse('Dokter tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Gagal mengubah status ketersediaan dokter
            return $this->errorResponse('Gagal mengubah status dokter', 500);
        }
    }

    // Menghapus profil dokter secara soft delete
    // Akses: admin saja
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->doctorService->delete($id);
            return $this->successResponse(null, 'Profil dokter berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Dokter dengan ID yang diberikan tidak ditemukan
            return $this->errorResponse('Dokter tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Gagal menghapus profil dokter
            return $this->errorResponse('Gagal menghapus profil dokter', 500);
        }
    }
}