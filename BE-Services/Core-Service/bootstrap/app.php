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
        // 'auth:sanctum' → validasi Sanctum token dari client (built-in Laravel)
        // 'role'         → otorisasi berdasarkan kolom role di tabel users
        //
        // Catatan: Core Service menggunakan auth:sanctum langsung karena
        // Core Service adalah satu-satunya service yang memiliki tabel users
        // dan personal_access_tokens. Service lain cukup pakai X-Service-Token.
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
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
        // Terjadi saat service melempar exception
        // contoh: throw new \Exception('Pesan error.', 422);
        $exceptions->render(function (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        });
    })->create();