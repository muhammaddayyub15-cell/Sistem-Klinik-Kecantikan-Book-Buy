<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateGateway
{
    /**
     * Validasi token ke Core Service sebelum request diteruskan.
     * Gateway tidak punya DB sendiri — validasi didelegasikan ke Core Service.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Token not provided.',
            ], 401);
        }

        try {
            $coreUrl  = config('services.core_service.base_url');
            $response = Http::withToken($token)
                ->timeout(10) // FIX: tambah timeout agar tidak hang selamanya
                ->get("{$coreUrl}/api/auth/validate-token");

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Invalid or expired token.',
                ], 401);
            }

            // Pakai attributes bukan merge()
            // attributes = metadata internal Laravel, TIDAK ikut ke request body/query
            // merge()    = masuk ke request input → ikut ke-forward ke downstream service 
            $userData = $response->json('data');
            \Log::info('AUTH USER DATA', $userData ?? []); // untuk test perlu hapus
            $request->attributes->set('_auth_user', $userData);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Auth service unavailable.',
                'error'   => config('app.debug') ? $e->getMessage() : null, // debug only
            ], 503);
        }

        return $next($request);
    }
}