<?php

use App\Http\Controllers\GatewayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Gateway Routes
|
| Konvensi prefix:
|   /api/core/...     → Core Service
|   /api/products/... → Product Service
|   /api/orders/...   → Order & Payment Service
|   /api/reports/...  → Report Service
|
| Middleware:
|   auth.gateway → validasi token (wajib untuk semua route terproteksi)
|   role:X       → cek role, selalu setelah auth.gateway
|--------------------------------------------------------------------------
*/

// ── Core Service — PUBLIC & PROTECTED ───────────────────────────────────
// auth/login dan auth/register otomatis public karena tidak
// masuk group auth.gateway. Wildcard {path} cover semua route core.
Route::any('core/{path}', [GatewayController::class, 'coreProxy'])
    ->where('path', '.*');

// ── PROTECTED — wajib login ──────────────────────────────────────────────
Route::middleware('auth.gateway')->group(function () {

    // Product Service — admin & doctor
    Route::middleware('role:admin,doctor')
        ->any('products/{path}', [GatewayController::class, 'productProxy'])
        ->where('path', '.*');

    // Order & Payment Service — semua role
    Route::any('orders/{path}', [GatewayController::class, 'orderProxy'])
        ->where('path', '.*');

    // Report Service — admin only
    Route::middleware('role:admin')
        ->any('reports/{path}', [GatewayController::class, 'reportProxy'])
        ->where('path', '.*');
});