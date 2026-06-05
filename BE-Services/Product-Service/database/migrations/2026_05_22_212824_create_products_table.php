<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name');
            $table->string('SKU')->unique();
            // FK ke product_categories di database product-service dibuat setelah Product Service berjalan
            $table->foreignId('category_id')->constrained('product_categories')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->integer('stock_qty')->default(0);
            $table->string('unit');
            $table->decimal('rating', 3, 1)->nullable();
            $table->string('tag')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};