<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Service\Service;

class ServiceCategory extends Model
{
    use HasFactory;

    // Nama tabel sesuai migration
    protected $table = 'service_categories';

    // Primary key custom
    protected $primaryKey = 'category_id';

    // Pakai timestamps (karena migration pakai timestamps)
    public $timestamps = true;

    protected $fillable = [
        'category_name',
        'description',
    ];

    // Relasi 1:N (Category → Services)
    public function services(): HasMany
    {
        return $this->hasMany(
            Service::class,
            'category_id', // FK di table service
            'category_id'  // PK di table ini
        );
    }
}