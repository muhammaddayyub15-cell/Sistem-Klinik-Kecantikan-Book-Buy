<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class ProxyService
{
    public function forward(Request $request, string $targetUrl)
    {
        try {
            $method = strtoupper($request->method());

            // Hanya kirim body untuk method yang memang punya body
            $hasBody = in_array($method, ['POST', 'PUT', 'PATCH']);

            $http = Http::withHeaders([
                'Authorization'      => $request->header('Authorization'),
                'Accept'             => 'application/json',
                'X-Internal-Service' => 'api-gateway',
                'X-Service-Token'    => config('services.internal_token'),
                'X-User-Id'   => $request->attributes->get('_auth_user')['user_id'] ?? null,
                'X-User-Role' => $request->attributes->get('_auth_user')['role'] ?? null,
                'X-User-Name'        => $request->attributes->get('_auth_user.name'),
            ])->timeout(30);

            \Log::info('GATEWAY FORWARD', [
                'method'   => $method,
                'url'      => $targetUrl,
                'hasBody'  => $hasBody,
                'body'     => $hasBody ? $request->all() : null,
                'query'    => $request->query(),
            ]);

            $options = ['query' => $request->query()];

            if ($hasBody) {
                // Cek apakah ada file upload
                if (count($request->allFiles()) > 0) {
                    $options['multipart'] = $this->buildMultipart($request);
                } else {
                    $options['json'] = $request->all();
                }
            }

            $response = $http->send($method, $targetUrl, $options);

            return response()->json(
                $response->json(),
                $response->status()
            );

        } catch (RequestException $e) {
            \Log::error('GATEWAY REQUEST ERROR', [
                'url'   => $targetUrl,
                'error' => $e->getMessage(),
                'code'  => $e->getCode(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Service unavailable',
                'error'   => $e->getMessage(),
            ], 503);

        } catch (\Exception $e) {
            \Log::error('GATEWAY ERROR', [
                'url'   => $targetUrl,
                'error' => $e->getMessage(),
            ]);

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

    private function buildMultipart(Request $request): array
    {
        $multipart = [];

        foreach ($request->all() as $key => $value) {
            if (!$request->hasFile($key)) {
                $multipart[] = ['name' => $key, 'contents' => $value];
            }
        }

        foreach ($request->allFiles() as $key => $file) {
            $multipart[] = [
                'name'     => $key,
                'contents' => fopen($file->getPathname(), 'r'),
                'filename' => $file->getClientOriginalName(),
            ];
        }

        return $multipart;
    }
}