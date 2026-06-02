<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id('presc_id');
            $table->foreignId('record_id')
                  ->constrained('medical_records', 'record_id')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('product_id');        // snapshot, bukan FK ke Product DB
            $table->string('product_name_snapshot');         // snapshot nama produk saat resep dibuat
            $table->unsignedInteger('qty');
            $table->text('dosage_instruction')->nullable();
            $table->timestamp('prescribed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};