<?php

namespace App\Providers;

use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use App\Models\Product\StockLog;
use App\Service\ProductCategoryService;
use App\Service\ProductService;
use App\Service\Repositories\ProductCategoryRepository;
use App\Service\Repositories\ProductRepository;
use App\Service\Repositories\StockLogRepository;
use Illuminate\Support\ServiceProvider;

// AppServiceProvider: Binding manual di IoC Container Laravel untuk Product Service.
//
// MENGAPA BINDING MANUAL?
// Semua Repository di-inject ke Service, dan Service di-inject ke Controller.
// Laravel tidak bisa otomatis resolve ini karena BaseRepository butuh $model yang konkret.
// Solusi: daftarkan binding di sini agar Container tahu cara membuat setiap instance.
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // --- Binding Repository ---

        // ProductCategoryRepository membutuhkan instance ProductCategory model
        $this->app->bind(ProductCategoryRepository::class, function () {
            return new ProductCategoryRepository(new ProductCategory());
        });

        // ProductRepository membutuhkan instance Product model
        $this->app->bind(ProductRepository::class, function () {
            return new ProductRepository(new Product());
        });

        // StockLogRepository membutuhkan instance StockLog model
        $this->app->bind(StockLogRepository::class, function () {
            return new StockLogRepository(new StockLog());
        });

        // --- Binding Service ---

        // ProductCategoryService bergantung pada ProductCategoryRepository
        $this->app->bind(ProductCategoryService::class, function ($app) {
            return new ProductCategoryService(
                $app->make(ProductCategoryRepository::class)
            );
        });

        // ProductService bergantung pada dua repository sekaligus
        $this->app->bind(ProductService::class, function ($app) {
            return new ProductService(
                $app->make(ProductRepository::class),
                $app->make(StockLogRepository::class)
            );
        });
    }

    public function boot(): void
    {
        // Tidak ada konfigurasi boot yang diperlukan saat ini
    }
}