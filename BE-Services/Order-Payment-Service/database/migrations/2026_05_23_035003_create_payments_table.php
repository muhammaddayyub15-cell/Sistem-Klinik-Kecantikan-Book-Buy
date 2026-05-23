<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // FK ke orders — relasi 1:1, satu order hanya punya satu payment
            $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete();
            // ID transaksi dari Midtrans — nullable saat payment baru diinisiasi
            $table->string('midtrans_id')->nullable()->unique();
            $table->decimal('amount', 12, 2);
            // Diisi setelah user memilih metode di halaman Midtrans
            $table->string('payment_methods')->nullable();
            $table->string('payment_channel')->nullable();
            // Status dari Midtrans: pending, settlement, expire, cancel, deny
            $table->enum('status', ['pending', 'settlement', 'expire', 'cancel', 'deny'])->default('pending');
            // Diisi saat Midtrans kirim webhook settlement
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};