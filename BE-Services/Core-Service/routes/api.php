<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Doctor\DoctorController;
use App\Http\Controllers\Medical\MedicalController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\Service\ServiceController;
use Illuminate\Support\Facades\Route;


// ── Auth Routes ────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {

    // Publik — tidak butuh token
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    // Private — butuh token Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
    });
});

// ── Doctor Routes ──────────────────────────────────────────────────────
Route::prefix('doctors')->group(function () {

    // Publik — tidak butuh token, untuk keperluan pasien melihat daftar dokter
    Route::get('/',                         [DoctorController::class, 'index']);
    Route::get('/available',                [DoctorController::class, 'getAvailable']);
    Route::get('/specialization/{specId}',  [DoctorController::class, 'getBySpecialization']);
    Route::get('/{id}',                     [DoctorController::class, 'show']);

    // Private — butuh token Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        // Buat profil dokter baru — hanya admin
        Route::post('/', [DoctorController::class, 'store'])
            ->middleware('role:admin');
        // Update profil dokter — hanya admin
        Route::put('/{id}', [DoctorController::class, 'update'])
            ->middleware('role:admin');
        // Toggle ketersediaan dokter — hanya admin
        Route::patch('/{id}/availability', [DoctorController::class, 'toggleAvailability'])
            ->middleware('role:admin');
        // Hapus profil dokter — hanya admin
        Route::delete('/{id}', [DoctorController::class, 'destroy'])
            ->middleware('role:admin');
    });
});

// ── Patient Routes ─────────────────────────────────────────────────────
// Semua route patient wajib login
Route::prefix('patients')->middleware('auth:sanctum')->group(function () {

    // Ambil semua pasien — hanya admin dan dokter
    Route::get('/', [PatientController::class, 'index'])
        ->middleware('role:admin,doctor');
    // Buat profil pasien baru — hanya admin
    // Catatan: pasien register via /auth/register, bukan endpoint ini
    Route::post('/', [PatientController::class, 'store'])
        ->middleware('role:admin');
    // Detail profil pasien — admin, dokter, dan pasien yang bersangkutan
    Route::get('/{id}', [PatientController::class, 'show'])
        ->middleware('role:admin,doctor,patient');
    // Update profil pasien — admin dan pasien yang bersangkutan
    Route::put('/{id}', [PatientController::class, 'update'])
        ->middleware('role:admin,patient');
    // Hapus profil pasien — hanya admin
    Route::delete('/{id}', [PatientController::class, 'destroy'])
        ->middleware('role:admin');
});


// ── Service Routes ─────────────────────────────────────────────────────
Route::prefix('services')->group(function () {

    // Publik — pasien bisa lihat daftar layanan tanpa login
    Route::get('/',     [ServiceController::class, 'index']);
    Route::get('/{id}', [ServiceController::class, 'show']);

    // Private — hanya admin yang bisa kelola layanan
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/',        [ServiceController::class, 'store']);
        Route::put('/{id}',     [ServiceController::class, 'update']);
        Route::delete('/{id}',  [ServiceController::class, 'destroy']);
    });
});

// ── Booking Routes ─────────────────────────────────────────────────────
// Semua route booking wajib login terlebih dahulu via Sanctum
Route::prefix('bookings')->middleware('auth:sanctum')->group(function () {

    // Ambil semua booking — hanya admin dan dokter
    Route::get('/', [BookingController::class, 'index'])
        ->middleware('role:admin,doctor');
    // Buat booking baru — hanya patient dan admin
    Route::post('/', [BookingController::class, 'store'])
        ->middleware('role:admin,patient');
    // Booking berdasarkan pasien — admin dan patient
    Route::get('/patient/{patientId}', [BookingController::class, 'getByPatient'])
        ->middleware('role:admin,patient');
    // Booking berdasarkan dokter — admin dan dokter
    Route::get('/doctor/{doctorId}', [BookingController::class, 'getByDoctor'])
        ->middleware('role:admin,doctor');
    // Detail satu booking — semua role boleh, ownership dicek di service
    Route::get('/{id}', [BookingController::class, 'show'])
        ->middleware('role:admin,doctor,patient');
    // Update status booking — hanya dokter dan admin
    Route::patch('/{id}/status', [BookingController::class, 'updateStatus'])
        ->middleware('role:admin,doctor');
    // Hapus booking — hanya admin
    Route::delete('/{id}', [BookingController::class, 'destroy'])
        ->middleware('role:admin');
});

// ── Medical Routes ─────────────────────────────────────────────────────
// Semua route medical wajib login terlebih dahulu via Sanctum
Route::prefix('medical-records')->middleware('auth:sanctum')->group(function () {
    // Ambil semua rekam medis — hanya admin dan dokter
    Route::get('/', [MedicalController::class, 'index'])
        ->middleware('role:admin,doctor');
    // Buat rekam medis baru — hanya dokter
    Route::post('/', [MedicalController::class, 'store'])
        ->middleware('role:doctor');
    // Catatan: route spesifik harus di atas route parameter /{id} agar tidak bentrok
    Route::get('/patient/{patientId}', [MedicalController::class, 'getByPatient'])
        ->middleware('role:admin,doctor,patient');
    Route::get('/doctor/{doctorId}', [MedicalController::class, 'getByDoctor'])
        ->middleware('role:admin,doctor');
    Route::get('/booking/{bookingId}', [MedicalController::class, 'getByBooking'])
        ->middleware('role:admin,doctor,patient');
    // Detail rekam medis — admin, dokter, patient yang terkait
    Route::get('/{id}', [MedicalController::class, 'show'])
        ->middleware('role:admin,doctor,patient');
    // Update rekam medis — hanya dokter
    Route::put('/{id}', [MedicalController::class, 'update'])
        ->middleware('role:doctor');
    // Tambah resep ke rekam medis — hanya dokter
    Route::post('/{id}/prescriptions', [MedicalController::class, 'addPrescriptions'])
        ->middleware('role:doctor');
    // Ganti seluruh resep — hanya dokter
    Route::put('/{id}/prescriptions', [MedicalController::class, 'replacePrescriptions'])
        ->middleware('role:doctor');
    // Hapus rekam medis — hanya admin
    Route::delete('/{id}', [MedicalController::class, 'destroy'])
        ->middleware('role:admin');
});