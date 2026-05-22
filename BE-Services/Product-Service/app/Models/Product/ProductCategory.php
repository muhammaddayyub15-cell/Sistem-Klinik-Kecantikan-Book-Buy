<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Model ProductCategory: Merepresentasikan tabel PRODUCT_CATEGORIES di Product Service DB.
// Relasi: satu kategori memiliki banyak produk (1:N → ke Product).
class ProductCategory extends Model
{
    use SoftDeletes;

    protected $table = 'product_categories';

    protected $fillable = [
        'category_name',
        // Tambahkan kolom lain sesuai migrasi
    ];

    // products: Relasi 1:N ke tabel products.
    // Digunakan untuk eager load daftar produk dalam satu kategori.
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}