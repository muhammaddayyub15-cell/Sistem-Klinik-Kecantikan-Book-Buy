<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            // Jenis laporan — booking_stats, revenue_stats, product_stats, doctor_performance
            $table->enum('report_type', ['booking_stats', 'revenue_stats', 'product_stats', 'doctor_performance']);
            // Payload agregat dari service pengirim disimpan sebagai JSON
            $table->json('data_json');
            // Rentang periode laporan
            $table->date('period_start');
            $table->date('period_end');
            // Timestamp saat snapshot dibuat atau diperbarui
            $table->timestamp('generated_at');
            // Index untuk mempercepat query berdasarkan type dan periode
            $table->index(['report_type', 'period_start', 'period_end']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};