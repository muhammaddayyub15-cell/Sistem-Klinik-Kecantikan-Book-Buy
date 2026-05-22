<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medical\StoreMedicalRecordRequest;
use App\Http\Requests\Medical\StorePrescriptionRequest;
use App\Service\MedicalService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class MedicalController extends Controller
{
    use ApiResponseTrait;

    // Inject MedicalService melalui constructor
    public function __construct(protected MedicalService $medicalService) {}

    // Menampilkan semua rekam medis
    // Akses: admin dan dokter saja
    public function index(): JsonResponse
    {
        try {
            $records = $this->medicalService->getAllWithRelations();
            return $this->successResponse($records);
        } catch (\Exception $e) {
            // Gagal mengambil data rekam medis
            return $this->errorResponse('Gagal mengambil data rekam medis', 500);
        }
    }

    // Membuat rekam medis baru untuk satu booking
    // Akses: dokter saja
    // Catatan: satu booking hanya boleh memiliki satu rekam medis
    public function store(StoreMedicalRecordRequest $request): JsonResponse
    {
        try {
            $record = $this->medicalService->createRecord($request->validated());
            return $this->successResponse($record, 'Rekam medis berhasil dibuat', 201);
        } catch (\Exception $e) {
            // Gagal menyimpan rekam medis
            return $this->errorResponse('Gagal membuat rekam medis', 500);
        }
    }

    // Menampilkan detail rekam medis beserta resep
    // Akses: admin, dokter (miliknya), patient (miliknya)
    public function show(int $id): JsonResponse
    {
        try {
            $record = $this->medicalService->findOrFail($id);
            return $this->successResponse($record);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Rekam medis tidak ditemukan
            return $this->errorResponse('Rekam medis tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Gagal mengambil detail rekam medis
            return $this->errorResponse('Gagal mengambil detail rekam medis', 500);
        }
    }

    // Mengubah diagnosis atau prescription_text pada rekam medis
    // Akses: dokter saja
    // Catatan: booking_id, patient_id, doctor_id tidak bisa diubah
    public function update(StoreMedicalRecordRequest $request, int $id): JsonResponse
    {
        try {
            $record = $this->medicalService->updateRecord($id, $request->validated());
            return $this->successResponse($record, 'Rekam medis berhasil diperbarui');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Rekam medis tidak ditemukan
            return $this->errorResponse('Rekam medis tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Gagal memperbarui rekam medis
            return $this->errorResponse('Gagal memperbarui rekam medis', 500);
        }
    }

    // Menambahkan resep ke rekam medis yang sudah ada
    // Akses: dokter saja
    public function addPrescriptions(StorePrescriptionRequest $request, int $id): JsonResponse
    {
        try {
            $record = $this->medicalService->addPrescriptions($id, $request->validated()['prescriptions']);
            return $this->successResponse($record, 'Resep berhasil ditambahkan', 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Rekam medis tidak ditemukan
            return $this->errorResponse('Rekam medis tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Gagal menyimpan resep — kemungkinan error database transaction
            return $this->errorResponse('Gagal menambahkan resep', 500);
        }
    }

    // Mengganti seluruh resep pada rekam medis
    // Akses: dokter saja
    // Catatan: semua resep lama akan dihapus dan diganti resep baru
    public function replacePrescriptions(StorePrescriptionRequest $request, int $id): JsonResponse
    {
        try {
            $record = $this->medicalService->replacePrescriptions($id, $request->validated()['prescriptions']);
            return $this->successResponse($record, 'Resep berhasil diperbarui');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Rekam medis tidak ditemukan
            return $this->errorResponse('Rekam medis tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Gagal mengganti resep — kemungkinan error database transaction
            return $this->errorResponse('Gagal memperbarui resep', 500);
        }
    }

    // Menampilkan rekam medis berdasarkan pasien
    // Akses: admin, patient (miliknya sendiri)
    public function getByPatient(int $patientId): JsonResponse
    {
        try {
            $records = $this->medicalService->getByPatient($patientId);
            return $this->successResponse($records);
        } catch (\Exception $e) {
            // Gagal mengambil rekam medis berdasarkan pasien
            return $this->errorResponse('Gagal mengambil rekam medis pasien', 500);
        }
    }

    // Menampilkan rekam medis berdasarkan dokter
    // Akses: admin, dokter (miliknya sendiri)
    public function getByDoctor(int $doctorId): JsonResponse
    {
        try {
            $records = $this->medicalService->getByDoctor($doctorId);
            return $this->successResponse($records);
        } catch (\Exception $e) {
            // Gagal mengambil rekam medis berdasarkan dokter
            return $this->errorResponse('Gagal mengambil rekam medis dokter', 500);
        }
    }

    // Menampilkan rekam medis berdasarkan booking
    // Akses: admin, dokter, patient (yang terkait)
    public function getByBooking(int $bookingId): JsonResponse
    {
        try {
            $record = $this->medicalService->getByBooking($bookingId);
            return $this->successResponse($record);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Rekam medis untuk booking ini belum ada
            return $this->errorResponse('Rekam medis untuk booking ini tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Gagal mengambil rekam medis berdasarkan booking
            return $this->errorResponse('Gagal mengambil rekam medis', 500);
        }
    }

    // Menghapus rekam medis — soft delete
    // Akses: admin saja
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->medicalService->delete($id);
            return $this->successResponse(null, 'Rekam medis berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Rekam medis tidak ditemukan
            return $this->errorResponse('Rekam medis tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Gagal menghapus rekam medis
            return $this->errorResponse('Gagal menghapus rekam medis', 500);
        }
    }
}