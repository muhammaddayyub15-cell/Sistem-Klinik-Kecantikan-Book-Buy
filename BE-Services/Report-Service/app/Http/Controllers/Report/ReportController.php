<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\StoreSnapshotRequest;
use App\Service\ReportService;
use App\Service\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

// ReportController: Thin controller — hanya menerima request, memanggil service,
// dan mengembalikan response. Tidak ada business logic di sini.
class ReportController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private ReportService $reportService)
    {
    }

    // dashboard: GET /reports/dashboard
    // Ambil snapshot terbaru semua jenis laporan sekaligus untuk halaman dashboard admin.
    public function dashboard(): JsonResponse
    {
        $summary = $this->reportService->getDashboardSummary();
        return $this->successResponse($summary, 'Data dashboard berhasil diambil');
    }

    // index: GET /reports/{type}
    // Ambil semua snapshot historis untuk satu jenis laporan.
    // Digunakan untuk halaman detail laporan dengan riwayat per periode.
    public function index(string $type): JsonResponse
    {
        $reports = $this->reportService->getReportByType($type);
        return $this->successResponse($reports, 'Data laporan berhasil diambil');
    }

    // latest: GET /reports/{type}/latest
    // Ambil snapshot terbaru untuk satu jenis laporan.
    // Shortcut agar client tidak perlu sorting manual dari response index.
    public function latest(string $type): JsonResponse
    {
        $report = $this->reportService->getLatestByType($type);

        if (!$report) {
            return $this->notFoundResponse('Belum ada data laporan untuk jenis ini.');
        }

        return $this->successResponse($report, 'Data laporan terbaru berhasil diambil');
    }

    // storeSnapshot: POST /reports/snapshot
    // Menerima snapshot dari service lain (Core, Product, Order Service).
    // Endpoint ini dipanggil oleh internal service, bukan oleh client langsung.
    public function storeSnapshot(StoreSnapshotRequest $request): JsonResponse
    {
        $report = $this->reportService->storeSnapshot($request->validated());
        return $this->createdResponse($report, 'Snapshot laporan berhasil disimpan');
    }
}