<?php

namespace App\Http\Controllers;

use App\Services\ProxyService;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function __construct(protected ProxyService $proxy) {}

    // ── Core Service ────────────────────────────────────────────
    public function coreProxy(Request $request, string $path)
    {
        $url = $this->proxy->buildUrl('core_service', "api/{$path}");
        return $this->proxy->forward($request, $url);
    }

    // ── Product Service ─────────────────────────────────────────
    public function productProxy(Request $request, string $path)
    {
        $url = $this->proxy->buildUrl('product_service', "api/{$path}");
        return $this->proxy->forward($request, $url);
    }

    // ── Order & Payment Service ──────────────────────────────────
    public function orderProxy(Request $request, string $path)
    {
        $url = $this->proxy->buildUrl('order_service', "api/{$path}");
        return $this->proxy->forward($request, $url);
    }

    // ── Report Service ───────────────────────────────────────────
    public function reportProxy(Request $request, string $path)
    {
        $url = $this->proxy->buildUrl('report_service', "api/{$path}");
        return $this->proxy->forward($request, $url);
    }
}