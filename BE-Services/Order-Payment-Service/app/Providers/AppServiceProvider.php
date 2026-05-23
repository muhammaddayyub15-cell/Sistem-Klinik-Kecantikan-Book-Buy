<?php

namespace App\Providers;

use App\Models\Order\Order;
use App\Models\Order\OrderItem;
use App\Models\Payment\Payment;
use App\Service\OrderService;
use App\Service\PaymentService;
use App\Service\Repositories\OrderItemRepository;
use App\Service\Repositories\OrderRepository;
use App\Service\Repositories\PaymentRepository;
use Illuminate\Support\ServiceProvider;

// AppServiceProvider: Binding manual di IoC Container Laravel untuk Order-Payment Service.
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

        $this->app->bind(OrderRepository::class, function () {
            return new OrderRepository(new Order());
        });

        $this->app->bind(OrderItemRepository::class, function () {
            return new OrderItemRepository(new OrderItem());
        });

        $this->app->bind(PaymentRepository::class, function () {
            return new PaymentRepository(new Payment());
        });

        // --- Binding Service ---

        // OrderService bergantung pada dua repository sekaligus
        $this->app->bind(OrderService::class, function ($app) {
            return new OrderService(
                $app->make(OrderRepository::class),
                $app->make(OrderItemRepository::class)
            );
        });

        // PaymentService bergantung pada PaymentRepository dan OrderRepository
        // OrderRepository dibutuhkan untuk update status order setelah webhook settlement
        $this->app->bind(PaymentService::class, function ($app) {
            return new PaymentService(
                $app->make(PaymentRepository::class),
                $app->make(OrderRepository::class)
            );
        });
    }

    public function boot(): void
    {
        //
    }
}