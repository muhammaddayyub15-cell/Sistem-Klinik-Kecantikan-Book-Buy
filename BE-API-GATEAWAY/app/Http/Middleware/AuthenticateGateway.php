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
                ->get("{$coreUrl}/api/auth/validate-token");

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Invalid or expired token.',
                ], 401);
            }

            // Inject user data ke request agar bisa dipakai controller
            $userData = $response->json('data');
            $request->merge(['_auth_user' => $userData]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Auth service unavailable.',
            ], 503);
        }

        return $next($request);
    }
}