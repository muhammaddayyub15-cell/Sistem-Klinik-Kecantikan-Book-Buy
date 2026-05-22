<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// RoleMiddleware: Otorisasi berdasarkan role user untuk Product Service.
//
// CATATAN ARSITEKTUR:
// Role user tidak divalidasi dari token di sini — role sudah diekstrak oleh API Gateway
// dari JWT, lalu diteruskan via header X-User-Role ke semua service.
// Middleware ini hanya membaca header tersebut dan membandingkan dengan role yang diizinkan.
//
// Contoh penggunaan di route:
//   Route::post('/products', ...)->middleware('role:admin');
//   Route::get('/products', ...)->middleware('role:admin,doctor,patient');
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Ambil role dari header yang diteruskan oleh API Gateway
        $userRole = $request->header('X-User-Role');

        if (!$userRole || !in_array($userRole, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda tidak memiliki izin untuk mengakses resource ini.',
            ], 403);
        }

        return $next($request);
    }
}