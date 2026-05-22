<?php

namespace App\Models\Doctor;

use App\Models\User\User;
use App\Models\Booking\Booking;
use App\Models\Medical\MedicalRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'doctor_id';

    protected $fillable = [
        'user_id',
        'spec_id',
        'license_no',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
        ];
    }

    // Relasi dengan model lain

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class, 'spec_id', 'spec_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_id', 'doctor_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'doctor_id', 'doctor_id');
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'doctor_id', 'doctor_id');
    }
}