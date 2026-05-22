<?php

use App\Http\Controllers\Product\ProductCategoryController;
use App\Http\Controllers\Product\ProductController;
use Illuminate\Support\Facades\Route;


// ── Product Category Routes ────────────────────────────────────────────
Route::prefix('product-categories')->group(function () {

    // Publik — semua role bisa melihat daftar dan detail kategori
    Route::get('/',     [ProductCategoryController::class, 'index']);
    Route::get('/{id}', [ProductCategoryController::class, 'show']);

    // Private — hanya admin yang bisa kelola kategori
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/',        [ProductCategoryController::class, 'store']);
        Route::put('/{id}',     [ProductCategoryController::class, 'update']);
        Route::patch('/{id}',   [ProductCategoryController::class, 'update']);
        Route::delete('/{id}',  [ProductCategoryController::class, 'destroy']);
    });
});

// ── Product Routes ─────────────────────────────────────────────────────
Route::prefix('products')->group(function () {

    // Publik — semua role bisa melihat daftar dan detail produk
    Route::get('/',     [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);

    // Private — hanya admin yang bisa kelola produk
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/',        [ProductController::class, 'store']);
        Route::put('/{id}',     [ProductController::class, 'update']);
        Route::patch('/{id}',   [ProductController::class, 'update']);
        Route::delete('/{id}',  [ProductController::class, 'destroy']);

        // Catatan: endpoint stock dipisah dari update produk agar setiap perubahan
        // stok selalu tercatat di stock_logs — tidak boleh update stock_qty langsung
        // via PUT /products/{id}. Endpoint ini juga bisa dipanggil oleh Order Service
        // via internal API call setelah order selesai diproses.
        Route::post('/{id}/stock',      [ProductController::class, 'updateStock']);
        Route::get('/{id}/stock-logs',  [ProductController::class, 'stockLogs']);
    });
});