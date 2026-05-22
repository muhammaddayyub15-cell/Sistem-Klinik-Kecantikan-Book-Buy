<?php

namespace App\Models\Service;

use App\Models\Booking\Booking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    // Eksplisit nama tabel sesuai ERD
    protected $table = 'services';

    // PK sesuai ERD: service_id (bukan default 'id')
    protected $primaryKey = 'service_id';

    protected $fillable = [
        'category_id',

        // Sesuai ERD: service_name (bukan name)
        'service_name',

        'description',

        // Sesuai ERD: base_price (bukan price)
        'base_price',

        // deleted_at dikelola otomatis oleh SoftDeletes
    ];

    protected function casts(): array
    {
        return [
            // Cast ke decimal 2 angka di belakang koma
            'base_price' => 'decimal:2',
        ];
    }

    // Relasi ke ServiceCategory (N:1 — banyak layanan dalam satu kategori)
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id', 'category_id');
    }

    // Relasi ke Booking (1:N — satu layanan bisa dipilih di banyak booking)
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'service_id', 'service_id');
    }
}