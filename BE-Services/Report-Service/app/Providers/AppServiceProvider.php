<?php

namespace App\Providers;

use App\Models\Report\Report;
use App\Service\ReportService;
use App\Service\Repositories\ReportRepository;
use Illuminate\Support\ServiceProvider;

// AppServiceProvider: Binding manual di IoC Container Laravel untuk Report Service.
//
// MENGAPA BINDING MANUAL?
// Repository di-inject ke Service, dan Service di-inject ke Controller.
// Laravel tidak bisa otomatis resolve ini karena BaseRepository butuh $model yang konkret.
// Solusi: daftarkan binding di sini agar Container tahu cara membuat setiap instance.
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Binding ReportRepository dengan model Report
        $this->app->bind(ReportRepository::class, function () {
            return new ReportRepository(new Report());
        });

        // Binding ReportService dengan ReportRepository
        $this->app->bind(ReportService::class, function ($app) {
            return new ReportService(
                $app->make(ReportRepository::class)
            );
        });
    }

    public function boot(): void
    {
        //
    }
}