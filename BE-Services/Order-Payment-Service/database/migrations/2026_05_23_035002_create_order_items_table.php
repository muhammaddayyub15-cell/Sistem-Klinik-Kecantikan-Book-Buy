<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            // FK ke tabel orders — satu-satunya FK di service ini
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            // Snapshot data produk dari Product Service — tidak ada FK hidup ke Product DB
            $table->unsignedBigInteger('product_id_snapshot');
            $table->string('product_name_snapshot');
            // Harga saat transaksi — tidak berubah meski harga produk diupdate di Product Service
            $table->decimal('unit_price_snapshot', 12, 2);
            $table->integer('qty')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};