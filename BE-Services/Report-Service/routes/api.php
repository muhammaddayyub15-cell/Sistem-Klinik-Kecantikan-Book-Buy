<?php

use App\Http\Controllers\Report\ReportController;
use Illuminate\Support\Facades\Route;


// ── Report Routes ──────────────────────────────────────────────────────
// Semua route report wajib login terlebih dahulu via Sanctum
Route::prefix('reports')->middleware('auth:sanctum')->group(function () {

    // Dashboard summary — hanya admin
    // Catatan: route spesifik harus di atas route parameter /{type} agar tidak bentrok
    Route::get('/dashboard', [ReportController::class, 'dashboard'])
        ->middleware('role:admin');

    // Terima snapshot dari service lain (Core, Product, Order Service)
    // Catatan: endpoint ini dipanggil oleh internal service, bukan client langsung
    // Verifikasi tambahan dilakukan via X-Service-Token di AuthenticateApi middleware
    Route::post('/snapshot', [ReportController::class, 'storeSnapshot'])
        ->middleware('role:admin');

    // Ambil snapshot terbaru untuk satu jenis laporan — hanya admin
    // Catatan: route /{type}/latest harus di atas /{type} agar tidak tertangkap sebagai {type}=latest
    Route::get('/{type}/latest', [ReportController::class, 'latest'])
        ->middleware('role:admin');

    // Ambil semua snapshot historis untuk satu jenis laporan — hanya admin
    // type: booking_stats | revenue_stats | product_stats | doctor_performance
    Route::get('/{type}', [ReportController::class, 'index'])
        ->middleware('role:admin');
});