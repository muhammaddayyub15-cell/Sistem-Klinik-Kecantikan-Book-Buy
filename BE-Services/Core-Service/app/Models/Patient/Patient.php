<?php

namespace App\Models\Patient;

use App\Models\User\User;
use App\Models\Booking\Booking;
use App\Models\Medical\MedicalRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    // Eksplisit nama tabel sesuai ERD (snake_case plural)
    protected $table = 'patients';

    // patient_id (bukan default 'id')
    protected $primaryKey = 'patient_id';

    protected $fillable = [
        'user_id',
        'date_of_birth',
        'gender',
        'blood_type',
        'address',
        'medical_history', // mesih berupa text, bisa diubah ke JSON jika diperlukan 
    ];

    protected function casts(): array
    {
        return [
            // Cast date_of_birth ke date agar bisa dipanggil sebagai Carbon
            'date_of_birth' => 'date',
        ];
    }

    // Relasi ke User (1:1 — satu pasien punya satu akun)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Booking (1:N — satu pasien bisa punya banyak booking)
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'patient_id');
    }

    // Relasi ke MedicalRecord (1:N — satu pasien bisa punya banyak rekam medis)
    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'patient_id');
    }
}