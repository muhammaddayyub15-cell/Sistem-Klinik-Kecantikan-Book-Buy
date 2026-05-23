<?php

namespace App\Service\Shared\Traits;

use Illuminate\Http\JsonResponse;

// ApiResponseTrait: Standarisasi format JSON response untuk seluruh endpoint Order-Payment Service.
// Digunakan di semua Controller agar response konsisten:
// { success, message, data, errors } dengan HTTP status code yang tepat.
trait ApiResponseTrait
{
    // successResponse: Untuk response berhasil (2xx).
    protected function successResponse(
        mixed $data = null,
        string $message = 'Berhasil',
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    // createdResponse: Shortcut untuk HTTP 201 saat resource baru berhasil dibuat.
    protected function createdResponse(
        mixed $data = null,
        string $message = 'Data berhasil dibuat'
    ): JsonResponse {
        return $this->successResponse($data, $message, 201);
    }

    // errorResponse: Untuk response gagal (4xx / 5xx).
    // $errors opsional, diisi saat validasi gagal (array field => message).
    protected function errorResponse(
        string $message = 'Terjadi kesalahan',
        int $statusCode = 400,
        mixed $errors = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $statusCode);
    }

    // notFoundResponse: Shortcut untuk HTTP 404.
    protected function notFoundResponse(string $message = 'Data tidak ditemukan'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }
}