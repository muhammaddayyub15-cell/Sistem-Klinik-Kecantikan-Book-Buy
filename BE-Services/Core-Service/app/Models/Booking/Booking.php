<?php

namespace App\Models\Booking;

use App\Models\Patient\Patient;
use App\Models\Doctor\Doctor;
use App\Models\Doctor\DoctorSchedule;
use App\Models\Service\Service;
use App\Models\Medical\MedicalRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'doctsched_id',
        'service_id',
        'booked_date',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'booked_date' => 'date',
            'start_time'  => 'datetime:H:i',
            'end_time'    => 'datetime:H:i',
        ];
    }

    // relasi dengan model lain

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'id');
    }

    public function doctorSchedule(): BelongsTo
    {
        return $this->belongsTo(DoctorSchedule::class, 'doctsched_id', 'id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class, 'booking_id', 'id');
    }
}