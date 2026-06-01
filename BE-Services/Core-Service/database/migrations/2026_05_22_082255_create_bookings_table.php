<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->foreignId('patient_id')
                  ->constrained('patients', 'patient_id')
                  ->onDelete('restrict');
            $table->foreignId('doctor_id')
                  ->constrained('doctors', 'doctor_id')
                  ->onDelete('restrict');
            $table->foreignId('doctor_schedule_id')
                  ->constrained('doctor_schedules', 'schedule_id')
                  ->onDelete('restrict');
            $table->foreignId('service_id')
                  ->constrained('services', 'service_id')
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