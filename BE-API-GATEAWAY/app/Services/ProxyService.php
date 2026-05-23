<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class ProxyService
{
    public function forward(Request $request, string $targetUrl): \Illuminate\Http\JsonResponse
    {
        try {
            $response = Http::withHeaders([
                'Authorization'      => $request->header('Authorization'),
                'Accept'             => 'application/json',
                'X-Internal-Service' => 'api-gateway',
                'X-Service-Token'    => config('services.internal_token'),
                'X-User-Id'          => $request->input('_auth_user.user_id'),
                'X-User-Role'        => $request->input('_auth_user.role'),   
                'X-User-Name'        => $request->input('_auth_user.name'), 
            ])
            ->timeout(30)
            ->{strtolower($request->method())}(
                $targetUrl,
                $request->all()
            );

            return response()->json(
                $response->json(),
                $response->status()
            );

        } catch (RequestException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service unavailable',
                'error'   => $e->getMessage(),
            ], 503);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gateway error',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function buildUrl(string $serviceKey, string $path): string
    {
        $baseUrl = config("services.{$serviceKey}.base_url");
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}