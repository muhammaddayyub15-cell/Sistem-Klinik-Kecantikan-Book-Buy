<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Booking\BookingController;
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