<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceCategory extends Model
{
    use HasFactory;

    // Eksplisit nama tabel sesuai ERD
    protected $table = 'services_categories';

    // PK sesuai ERD: category_id (bukan default 'id')
    protected $primaryKey = 'category_id';

    protected $fillable = [
        // Sesuai ERD: category_name 
        'category_name',

        // Field description dihapus karena tidak ada di ERD
    ];

    // Relasi ke Service (1:N — satu kategori punya banyak layanan)
    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'category_id', 'category_id');
    }
}