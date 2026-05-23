<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// AuthenticateApi: Middleware autentikasi untuk Report Service.
//
// CATATAN ARSITEKTUR:
// Dalam sistem microservice ini, autentikasi utama dilakukan di API Gateway.
// Report Service menerima dua jenis request:
//   1. Dari client (admin) via API Gateway → membawa X-User-Role: admin
//   2. Dari service lain (Core, Product, Order) → membawa X-Service-Token
// Middleware ini memvalidasi X-Service-Token untuk memastikan request
// hanya berasal dari Gateway atau service internal yang terpercaya.
class AuthenticateApi
{
    public function handle(Request $request, Closure $next): Response
    {
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