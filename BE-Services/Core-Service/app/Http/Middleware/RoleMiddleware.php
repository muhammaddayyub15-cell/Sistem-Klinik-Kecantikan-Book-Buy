<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * handle: cek apakah role user sesuai dengan role yang diizinkan
     * @param  string  ...$roles  Daftar role yang diizinkan (dipisah koma)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // pastikan user sudah terautentikasi
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // cek apakah role user ada di daftar role yang diizinkan
        if (!in_array($user->role, $roles)) {
            return response()->json([
                'message'        => 'Forbidden. You do not have access to this resource.',
                'your_role'      => $user->role,
                'required_roles' => $roles,
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}