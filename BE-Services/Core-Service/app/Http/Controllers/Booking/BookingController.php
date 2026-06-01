<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Requests\Booking\UpdateBookingStatusRequest;
use App\Service\BookingService;
use App\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    use ApiResponseTrait;

    // Inject BookingService melalui constructor
    public function __construct(protected BookingService $bookingService) {}

    // Menampilkan semua data booking
    // Akses: admin (semua booking), doctor (booking miliknya saja)
   public function index(Request $request): JsonResponse
{
    try {
        $bookings = $this->bookingService->getAllWithRelations($request->user());
        return $this->successResponse($bookings);
    } catch (\Exception $e) {
        return $this->errorResponse('Gagal mengambil data booking', 500);
    }
}

    // Membuat booking baru
    // Akses: patient (booking untuk dirinya sendiri), admin
    public function store(StoreBookingRequest $request): JsonResponse
    {
        try {
            $booking = $this->bookingService->createBooking($request->validated());
            return $this->successResponse($booking, 'Booking berhasil dibuat', 201);
        } catch (ValidationException $e) {
            // Slot jadwal sudah terisi atau validasi bisnis gagal
            return $this->errorResponse($e->getMessage(), 422, $e->errors());
        } catch (\Exception $e) {
            // Gagal menyimpan booking ke database
            return $this->errorResponse('Gagal membuat booking', 500);
        }
    }

    // Menampilkan detail satu booking berdasarkan ID
    // Akses: admin, doctor (booking miliknya), patient (booking miliknya)
    public function show(int $id): JsonResponse
    {
        try {
            $booking = $this->bookingService->findOrFail($id);
            return $this->successResponse($booking);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Booking dengan ID yang diberikan tidak ditemukan
            return $this->errorResponse('Booking tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Gagal mengambil detail booking
            return $this->errorResponse('Gagal mengambil detail booking', 500);
        }
    }

    // Mengubah status booking (misal: confirmed, completed, cancelled)
    // Akses: doctor (untuk booking-nya), admin
    // Catatan: patient tidak boleh mengubah status booking secara langsung
    public function updateStatus(UpdateBookingStatusRequest $request, int $id): JsonResponse
    {
        try {
            $booking = $this->bookingService->updateStatus($id, $request->validated());
            return $this->successResponse($booking, 'Status booking berhasil diperbarui');
        } catch (ValidationException $e) {
            // Status tidak valid atau booking sudah selesai/dibatalkan
            return $this->errorResponse($e->getMessage(), 422, $e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Booking dengan ID yang diberikan tidak ditemukan
            return $this->errorResponse('Booking tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Gagal memperbarui status booking
            return $this->errorResponse('Gagal memperbarui status booking', 500);
        }
    }

    // Menampilkan semua booking milik pasien tertentu
    // Akses: admin, patient (hanya miliknya sendiri)
    public function getByPatient(int $patientId): JsonResponse
    {
        try {
            $bookings = $this->bookingService->getByPatient($patientId);
            return $this->successResponse($bookings);
        } catch (\Exception $e) {
            // Gagal mengambil data booking berdasarkan pasien
            return $this->errorResponse('Gagal mengambil booking pasien', 500);
        }
    }

    // Menampilkan semua booking milik dokter tertentu
    // Akses: admin, doctor (hanya miliknya sendiri)
    public function getByDoctor(int $doctorId): JsonResponse
    {
        try {
            $bookings = $this->bookingService->getByDoctor($doctorId);
            return $this->successResponse($bookings);
        } catch (\Exception $e) {
            // Gagal mengambil data booking berdasarkan dokter
            return $this->errorResponse('Gagal mengambil booking dokter', 500);
        }
    }

    // Menghapus booking secara soft delete
    // Akses: admin saja
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->bookingService->delete($id);
            return $this->successResponse(null, 'Booking berhasil dihapus');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Booking dengan ID yang diberikan tidak ditemukan
            return $this->errorResponse('Booking tidak ditemukan', 404);
        } catch (\Exception $e) {
            // Gagal menghapus booking
            return $this->errorResponse('Gagal menghapus booking', 500);
        }
    }
}