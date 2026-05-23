<?php

namespace App\Service\Repositories;

use App\Models\Report\Report;
use App\Service\Shared\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

// ReportRepository: Menangani semua query ke tabel reports.
//
// CATATAN MIGRASI KE REALTIME:
// Jika suatu saat strategi berubah dari Snapshot ke Realtime (API call),
// cukup ubah implementasi method di repository ini.
// ReportService, ReportController, dan routes/api.php tidak perlu disentuh.
class ReportRepository extends BaseRepository
{
    public function __construct(Report $model)
    {
        parent::__construct($model);
    }

    // findByType: Ambil semua snapshot berdasarkan jenis laporan.
    // Diurutkan dari periode terbaru.
    public function findByType(string $reportType): Collection
    {
        return $this->model
            ->where('report_type', $reportType)
            ->orderBy('period_start', 'desc')
            ->get();
    }

    // findByTypeAndPeriod: Ambil snapshot spesifik berdasarkan type dan periode.
    // Digunakan untuk cek apakah snapshot periode tertentu sudah ada sebelum insert baru.
    public function findByTypeAndPeriod(string $reportType, string $periodStart, string $periodEnd): ?Report
    {
        return $this->model
            ->where('report_type', $reportType)
            ->where('period_start', $periodStart)
            ->where('period_end', $periodEnd)
            ->first();
    }

    // upsert: Simpan snapshot baru atau update jika sudah ada untuk type dan periode yang sama.
    // Menghindari duplikasi snapshot untuk periode yang sama.
    public function upsert(string $reportType, string $periodStart, string $periodEnd, array $dataJson): Report
    {
        $existing = $this->findByTypeAndPeriod($reportType, $periodStart, $periodEnd);

        if ($existing) {
            // Update snapshot yang sudah ada dengan data terbaru
            $existing->update([
                'data_json'    => $dataJson,
                'generated_at' => now(),
            ]);
            return $existing->fresh();
        }

        // Buat snapshot baru jika belum ada untuk periode ini
        return $this->model->create([
            'report_type'  => $reportType,
            'data_json'    => $dataJson,
            'period_start' => $periodStart,
            'period_end'   => $periodEnd,
            'generated_at' => now(),
        ]);
    }

    // findLatestByType: Ambil snapshot terbaru untuk satu jenis laporan.
    // Digunakan untuk dashboard yang hanya butuh data terkini.
    public function findLatestByType(string $reportType): ?Report
    {
        return $this->model
            ->where('report_type', $reportType)
            ->orderBy('period_start', 'desc')
            ->first();
    }

    // findAllLatest: Ambil snapshot terbaru untuk semua jenis laporan sekaligus.
    // Digunakan untuk endpoint dashboard summary.
    public function findAllLatest(): Collection
    {
        // Ambil satu snapshot terbaru per report_type menggunakan subquery
        return $this->model
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('reports')
                    ->groupBy('report_type');
            })
            ->get();
    }
}