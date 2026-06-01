<?php

namespace App\Http\Controllers;

use App\Services\ProxyService;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function __construct(protected ProxyService $proxy) {}

    // ── Core Service ─────────────────────────────────────────────────────
    // Selalu pakai $request->path() karena sudah include full path
    // Contoh: /api/auth/register → buildUrl → http://localhost:8001/api/auth/register
    public function coreProxy(Request $request, string $path = '')
    {
        $url = $this->proxy->buildUrl('core_service', $request->path());
        return $this->proxy->forward($request, $url);
    }

    // ── Product Service ───────────────────────────────────────────────────
    // Sama seperti coreProxy, pakai $request->path() langsung
    public function productProxy(Request $request, string $path = '')
    {
        $url = $this->proxy->buildUrl('product_service', $request->path());
        return $this->proxy->forward($request, $url);
    }

    // ── Order & Payment Service ───────────────────────────────────────────
    // Sama seperti coreProxy, pakai $request->path() langsung
    public function orderProxy(Request $request, string $path = '')
    {
        $url = $this->proxy->buildUrl('order_service', $request->path());
        return $this->proxy->forward($request, $url);
    }

    // ── Report Service ────────────────────────────────────────────────────
    // Sama seperti coreProxy, pakai $request->path() langsung
    public function reportProxy(Request $request, string $path = '')
    {
        $url = $this->proxy->buildUrl('report_service', $request->path());
        return $this->proxy->forward($request, $url);
    }
}