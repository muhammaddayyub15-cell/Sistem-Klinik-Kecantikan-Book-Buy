<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            // Snapshot data pasien dari Core Service — tidak ada FK hidup ke Core DB
            $table->unsignedBigInteger('patient_id_snapshot');
            $table->string('patient_name_snapshot');
            // Snapshot booking terkait — nullable karena order bisa dibuat tanpa booking
            $table->unsignedBigInteger('booking_id_snapshot')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            // Timestamp untuk setiap perubahan status penting
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};