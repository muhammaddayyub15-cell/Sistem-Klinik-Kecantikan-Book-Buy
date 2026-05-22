<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Daftarkan alias middleware agar bisa digunakan di routes/api.php
        // 'auth.api' → validasi X-Service-Token dari API Gateway
        // 'role'     → otorisasi berdasarkan X-User-Role dari API Gateway
        $middleware->alias([
            'auth.api' => \App\Http\Middleware\AuthenticateApi::class,
            'role'     => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // ModelNotFoundException → 404
        // Terjadi saat findOrFail() tidak menemukan data di repository
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
            ], 404);
        });

        // ValidationException → 422
        // Terjadi saat FormRequest gagal validasi
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $e->errors(),
            ], 422);
        });

        // Exception → dynamic status code
        // Terjadi saat service melempar exception (misal: stok tidak cukup, SKU duplikat)
        // $e->getCode() dipakai sebagai HTTP status — pastikan selalu diisi saat throw Exception
        // contoh: throw new \Exception('Pesan error.', 422);
        $exceptions->render(function (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        });
    })->create();