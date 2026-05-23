<?php

namespace App\Models\Report;

use Illuminate\Database\Eloquent\Model;

// Model Report: Merepresentasikan tabel REPORTS di Report Service DB.
//
// CATATAN ARSITEKTUR — SNAPSHOT PATTERN:
// Tabel ini menyimpan data agregat dari semua service dalam format JSON.
// Tidak ada FK ke service manapun — semua data disimpan sebagai snapshot read-only.
//
// Kolom utama:
//   - report_type  : jenis laporan (booking_stats, revenue_stats, product_stats, doctor_performance)
//   - data_json    : payload agregat dalam format JSON
//   - period_start : awal periode laporan (misal: 2025-01-01)
//   - period_end   : akhir periode laporan (misal: 2025-01-31)
//   - generated_at : timestamp saat snapshot dibuat/diperbarui
//
// Cara kerja:
//   1. Service lain (Core, Product, Order) mengirim POST ke /reports/snapshot
//      dengan data agregat mereka
//   2. Report Service menyimpan data tersebut ke tabel ini
//   3. Dashboard/analytics membaca data via GET /reports/{type}
//
// Jika suatu saat migrasi ke realtime, hanya ReportRepository dan ReportService
// yang perlu diubah — Model, Controller, dan Route tidak perlu disentuh.
class Report extends Model
{
    protected $table = 'reports';

    // Report tidak menggunakan SoftDeletes.
    // Snapshot lama di-overwrite saat ada snapshot baru dengan type dan periode yang sama.
    public $timestamps = true;

    protected $fillable = [
        'report_type',
        'data_json',
        'period_start',
        'period_end',
        'generated_at',
    ];

    protected $casts = [
        // Cast data_json otomatis ke array saat dibaca, ke JSON saat disimpan
        'data_json'    => 'array',
        'period_start' => 'date',
        'period_end'   => 'date',
        'generated_at' => 'datetime',
    ];
}