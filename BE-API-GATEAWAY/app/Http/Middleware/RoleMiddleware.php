<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Cek role user dari data yang sudah diinject AuthenticateGateway.
     * Gunakan setelah AuthenticateGateway di middleware stack.
     *
     * Contoh pemakaian di route:
     * ->middleware(['auth.gateway', 'role:admin,doctor'])
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $authUser = $request->input('_auth_user');
        $userRole = $authUser['role'] ?? null;

        if (!in_array($userRole, $roles)) {
            return response()->json([
                'success' => false,
                'message' => "Forbidden. Required role: " . implode(' or ', $roles),
            ], 403);
        }

        return $next($request);
    }
}