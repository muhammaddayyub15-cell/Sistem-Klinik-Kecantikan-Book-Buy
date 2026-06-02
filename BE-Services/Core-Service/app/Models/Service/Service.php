<?php

namespace App\Models\Service;

use App\Models\Booking\Booking;
use App\Models\Service\ServiceCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    // Table custom (singular)
    protected $table = 'service';

    // Primary key custom
    protected $primaryKey = 'service_id';

    // Pastikan PK auto increment
    public $incrementing = true;

    // Kalau tipe PK integer
    protected $keyType = 'int';

    protected $fillable = [
        'category_id',
        'service_name',
        'description',
        'base_price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'is_active'  => 'boolean',
        ];
    }

    /**
     * Relasi ke Category (N:1)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(
            ServiceCategory::class,
            'category_id',
            'category_id'
        );
    }

    /**
     * Relasi ke Booking (1:N)
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(
            Booking::class,
            'service_id',
            'service_id'
        );
    }
}