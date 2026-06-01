<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id('record_id');
            $table->foreignId('booking_id')
                  ->unique()
                  ->constrained('bookings', 'booking_id')
                  ->onDelete('restrict');
            $table->foreignId('patient_id')
                  ->constrained('patients', 'patient_id')
                  ->onDelete('restrict');
            $table->foreignId('doctor_id')
                  ->constrained('doctors', 'doctor_id')
                  ->onDelete('restrict');
            $table->text('diagnosis');
            $table->text('prescription_text')->nullable();
            $table->timestamp('recorded_at')->useCurrent();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};