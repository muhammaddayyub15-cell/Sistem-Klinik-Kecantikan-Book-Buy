<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id('doctor_id');
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users', 'user_id')
                  ->onDelete('cascade');
            $table->foreignId('spec_id')
                  ->constrained('specializations', 'spec_id')
                  ->onDelete('restrict');
            $table->string('license_no')->unique();
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_available')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};