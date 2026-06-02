<?php

namespace App\Models\Doctor;

use App\Models\User\User;
use App\Models\Doctor\DoctorSchedule;
use App\Models\Doctor\Specialization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'doctors';
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
            'deleted_at'   => 'datetime',
        ];
    }

    // Relasi ke User (identitas login)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi ke Specialization
    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class, 'spec_id', 'spec_id');
    }

    // Relasi ke semua jadwal dokter
    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_id', 'doctor_id');
    }

    // Relasi ke jadwal aktif saja (scope praktis untuk query)
    public function activeSchedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_id', 'doctor_id')
            ->where('is_active', true);
    }
}