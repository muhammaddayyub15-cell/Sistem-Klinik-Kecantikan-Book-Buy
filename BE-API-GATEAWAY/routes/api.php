<?php

use App\Http\Controllers\GatewayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Gateway Routes
|--------------------------------------------------------------------------
*/

// ── PUBLIC ROUTES ────────────────────────────────────────────────────────

// Auth (Core Service)
Route::any('auth/login', [GatewayController::class, 'coreProxy']);
Route::any('auth/register', [GatewayController::class, 'coreProxy']);

// Doctors & Services (Core Service)
Route::any('doctors/available', [GatewayController::class, 'coreProxy']);
Route::any('doctors/specialization/{specId}', [GatewayController::class, 'coreProxy']);
Route::any('doctors/{id}', [GatewayController::class, 'coreProxy'])->where('id', '[0-9]+');
Route::any('doctors', [GatewayController::class, 'coreProxy']);
Route::any('services/{id}', [GatewayController::class, 'coreProxy'])->where('id', '[0-9]+');
Route::any('services', [GatewayController::class, 'coreProxy']);

// Products & Categories (Product Service)
Route::any('products/{id}', [GatewayController::class, 'productProxy'])->where('id', '[0-9]+');
Route::any('products', [GatewayController::class, 'productProxy']);
Route::any('product-categories/{id}', [GatewayController::class, 'productProxy'])->where('id', '[0-9]+');
Route::any('product-categories', [GatewayController::class, 'productProxy']);

// Webhook (Order Service)
Route::any('payments/webhook', [GatewayController::class, 'orderProxy']);


// ── PROTECTED ROUTES (Wajib Login) ───────────────────────────────────────
Route::middleware('auth.gateway')->group(function () {

    // Auth (Core Service)
    Route::any('auth/logout', [GatewayController::class, 'coreProxy']);
    Route::any('auth/me', [GatewayController::class, 'coreProxy']);
    Route::any('auth/validate-token', [GatewayController::class, 'coreProxy']);

    // Bookings, Patients, Medical Records (Core Service)
    Route::any('bookings/{path?}', [GatewayController::class, 'coreProxy'])->where('path', '.*');
    Route::any('patients/{path?}', [GatewayController::class, 'coreProxy'])->where('path', '.*');
    Route::any('medical-records/{path?}', [GatewayController::class, 'coreProxy'])->where('path', '.*');

    // Doctor/Service mutations (Core Service)
    Route::middleware('role:admin')->group(function () {
        Route::post('doctors', [GatewayController::class, 'coreProxy']);
        Route::put('doctors/{id}', [GatewayController::class, 'coreProxy']);
        Route::patch('doctors/{id}/availability', [GatewayController::class, 'coreProxy']);
        Route::delete('doctors/{id}', [GatewayController::class, 'coreProxy']);

        Route::post('services', [GatewayController::class, 'coreProxy']);
        Route::put('services/{id}', [GatewayController::class, 'coreProxy']);
        Route::delete('services/{id}', [GatewayController::class, 'coreProxy']);
    });

    // Product Mutations (Product Service)
    Route::middleware('role:admin')->group(function () {
        Route::post('products', [GatewayController::class, 'productProxy']);
        Route::put('products/{id}', [GatewayController::class, 'productProxy']);
        Route::patch('products/{id}', [GatewayController::class, 'productProxy']);
        Route::delete('products/{id}', [GatewayController::class, 'productProxy']);
        Route::post('products/{id}/stock', [GatewayController::class, 'productProxy']);
        Route::get('products/{id}/stock-logs', [GatewayController::class, 'productProxy']);

        Route::post('product-categories', [GatewayController::class, 'productProxy']);
        Route::put('product-categories/{id}', [GatewayController::class, 'productProxy']);
        Route::patch('product-categories/{id}', [GatewayController::class, 'productProxy']);
        Route::delete('product-categories/{id}', [GatewayController::class, 'productProxy']);
    });

    // Orders & Payments (Order Service)
    Route::any('orders/{path?}', [GatewayController::class, 'orderProxy'])->where('path', '.*');
    Route::any('payments/{path?}', [GatewayController::class, 'orderProxy'])->where('path', '.*');

    // Reports (Report Service)
    Route::middleware('role:admin')->group(function () {
        Route::any('reports/{path?}', [GatewayController::class, 'reportProxy'])->where('path', '.*');
    });
});