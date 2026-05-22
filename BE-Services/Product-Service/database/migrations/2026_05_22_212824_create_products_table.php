<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('SKU')->unique();
            // FK ke product_categories — dibuat setelah tabel product_categories ada
            $table->foreignId('category_id')->constrained('product_categories')->cascadeOnDelete();
            $table->decimal('price', 12, 2)->default(0);
            $table->integer('stock_qty')->default(0);
            $table->string('unit'); // contoh: pcs, box, tablet, ml
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};