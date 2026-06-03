<?php

use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Payment\PaymentController;
use Illuminate\Support\Facades\Route;


// ── Order Routes ───────────────────────────────────────────────────────
// Semua route order wajib login terlebih dahulu via Sanctum
Route::prefix('orders')->middleware('auth.api')->group(function () {

    // Ambil semua order — hanya admin
    Route::get('/', [OrderController::class, 'index'])
        ->middleware('role:admin');
    // Buat order baru — admin dan patient
    Route::post('/', [OrderController::class, 'store'])
        ->middleware('role:admin,patient');
    // Catatan: route spesifik harus di atas route parameter /{id} agar tidak bentrok
    // Filter order berdasarkan status — hanya admin
    Route::get('/status/{status}', [OrderController::class, 'getByStatus'])
        ->middleware('role:admin');
    // Ambil order milik pasien tertentu — admin dan patient yang bersangkutan
    Route::get('/patient/{patientId}', [OrderController::class, 'getByPatient'])
        ->middleware('role:admin,patient');
    // Detail satu order — admin dan patient yang bersangkutan
    Route::get('/{id}', [OrderController::class, 'show'])
        ->middleware('role:admin,patient');
    // Update status order manual — hanya admin
    Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])
        ->middleware('role:admin');
});

// ── Payment Routes ─────────────────────────────────────────────────────
Route::prefix('payments')->group(function () {

    // Publik — tidak butuh auth karena dipanggil langsung oleh server Midtrans
    // Catatan: verifikasi keamanan dilakukan via signature key di PaymentService::handleWebhook()
    // Pastikan IP whitelist Midtrans dikonfigurasi di level server/firewall
    Route::post('/webhook', [PaymentController::class, 'webhook']);

    // Private — butuh token Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        // Inisiasi pembayaran — admin dan patient
        Route::post('/initiate', [PaymentController::class, 'initiate'])
            ->middleware('role:admin,patient');
        // Cek status pembayaran berdasarkan order — admin dan patient
        Route::get('/order/{orderId}', [PaymentController::class, 'show'])
            ->middleware('role:admin,patient');
    });
});