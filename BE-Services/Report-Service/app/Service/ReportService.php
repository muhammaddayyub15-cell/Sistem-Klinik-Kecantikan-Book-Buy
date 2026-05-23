<?php

namespace App\Service;

use App\Service\Repositories\ReportRepository;
use App\Service\Shared\Base\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

// ReportService: Business logic untuk manajemen snapshot laporan.
//
// CATATAN ARSITEKTUR — SNAPSHOT PATTERN:
// Report Service bersifat pasif — tidak menarik data dari service lain.
// Service lain (Core, Product, Order) yang mendorong snapshot ke sini
// via POST /reports/snapshot setelah event penting terjadi.
// Contoh event yang memicu snapshot:
//   - Core Service  : booking selesai → kirim booking_stats terbaru
//   - Order Service : order completed → kirim revenue_stats terbaru
//   - Product Service: stok berubah  → kirim product_stats terbaru
//
// Jenis laporan yang didukung (report_type):
//   - booking_stats      : statistik booking (total, per dokter, per layanan)
//   - revenue_stats      : statistik pendapatan (total, per periode)
//   - product_stats      : statistik penjualan produk (terlaris, stok menipis)
//   - doctor_performance : statistik kinerja dokter (jumlah pasien, rating)
class ReportService extends BaseService
{
    private ReportRepository $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        parent::__construct($reportRepository);
        $this->reportRepository = $reportRepository;
    }

    // getDashboardSummary: Ambil snapshot terbaru semua jenis laporan sekaligus.
    // Digunakan untuk halaman dashboard utama admin.
    public function getDashboardSummary(): Collection
    {
        return $this->reportRepository->findAllLatest();
    }

    // getReportByType: Ambil semua snapshot historis untuk satu jenis laporan.
    // Digunakan untuk halaman detail laporan dengan riwayat per periode.
    public function getReportByType(string $reportType): Collection
    {
        $this->validateReportType($reportType);
        return $this->reportRepository->findByType($reportType);
    }

    // getLatestByType: Ambil snapshot terbaru untuk satu jenis laporan.
    public function getLatestByType(string $reportType): ?Model
    {
        $this->validateReportType($reportType);
        return $this->reportRepository->findLatestByType($reportType);
    }

    // storeSnapshot: Terima dan simpan snapshot dari service lain.
    // Dipanggil saat service lain POST ke /reports/snapshot.
    // $data wajib mengandung: report_type, period_start, period_end, data
    //
    // Method ini menggunakan upsert — jika snapshot untuk periode yang sama
    // sudah ada, data akan diperbarui bukan diduplikasi.
    public function storeSnapshot(array $data): Model
    {
        $this->validateReportType($data['report_type']);

        return $this->reportRepository->upsert(
            reportType:  $data['report_type'],
            periodStart: $data['period_start'],
            periodEnd:   $data['period_end'],
            dataJson:    $data['data'],
        );
    }

    // validateReportType: Validasi bahwa report_type yang diterima adalah nilai yang valid.
    // Dipisah sebagai method private agar bisa dipakai ulang di semua method service.
    private function validateReportType(string $reportType): void
    {
        $validTypes = ['booking_stats', 'revenue_stats', 'product_stats', 'doctor_performance'];

        if (!in_array($reportType, $validTypes)) {
            throw new \Exception(
                'Jenis laporan tidak valid. Pilihan: ' . implode(', ', $validTypes) . '.', 422
            );
        }
    }
}