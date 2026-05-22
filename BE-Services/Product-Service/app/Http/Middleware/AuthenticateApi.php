<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// AuthenticateApi: Middleware autentikasi untuk Product Service.
//
// CATATAN ARSITEKTUR:
// Dalam sistem microservice ini, autentikasi utama dilakukan di API Gateway.
// Product Service menerima request yang sudah divalidasi oleh Gateway.
// Middleware ini memvalidasi internal service token (X-Service-Token)
// yang dikirim oleh Gateway atau service lain (misal Core Service / Order Service).
//
// JANGAN validasi JWT user langsung di sini — itu sudah dilakukan di Gateway.
// Header yang dikirim Gateway setelah validasi JWT:
//   - X-User-Id: ID user yang terautentikasi
//   - X-User-Role: role user (admin, doctor, patient)
//   - X-Service-Token: token internal antar service
class AuthenticateApi
{
    public function handle(Request $request, Closure $next): Response
    {
        // Validasi X-Service-Token dari Gateway atau service lain
        $serviceToken = $request->header('X-Service-Token');

        if (!$serviceToken || $serviceToken !== config('services.internal_token')) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diizinkan. Token service tidak valid.',
            ], 401);
        }

        return $next($request);
    }
}