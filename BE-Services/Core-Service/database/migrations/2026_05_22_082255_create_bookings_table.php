<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')
                  ->constrained('patients')
                  ->onDelete('restrict');
            $table->foreignId('doctor_id')
                  ->constrained('doctors')
                  ->onDelete('restrict');
            $table->foreignId('doctor_schedule_id')
                  ->constrained('doctor_schedules')
                  ->onDelete('restrict');
            $table->foreignId('service_id')
                  ->constrained('services')
                  ->onDelete('restrict');
            $table->date('booked_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', [
                'pending',
                'confirmed',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('pending');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};