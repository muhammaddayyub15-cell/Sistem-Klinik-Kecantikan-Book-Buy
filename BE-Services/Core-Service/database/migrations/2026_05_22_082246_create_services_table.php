<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')
                  ->constrained('service_categories')
                  ->onDelete('restrict');
            $table->string('name');
            $table->text('description')->nullable();
            $table->Decimal('base_price', 12, 2)->default(0);
            $table->string('unit')->nullable(); // contoh: "sesi", "30 menit"
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};