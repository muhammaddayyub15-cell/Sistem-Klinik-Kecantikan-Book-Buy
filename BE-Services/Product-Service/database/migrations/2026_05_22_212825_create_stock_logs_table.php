<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();
            // FK ke products
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            // Positif = stok masuk, negatif = stok keluar
            $table->integer('change_qty');
            $table->enum('type', ['in', 'out', 'adjustment']);
            // Opsional — diisi jika perubahan stok berasal dari order (reference ke order_id di Order Service)
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('notes')->nullable();
            // Tidak pakai softDeletes — log tidak boleh dihapus untuk menjaga audit trail
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
    }
};