<?php

namespace App\Service\Repositories;

use App\Models\Product\ProductCategory;
use App\Service\Shared\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

// ProductCategoryRepository: Menangani semua query ke tabel product_categories.
// Mewarisi operasi CRUD dasar dari BaseRepository.
class ProductCategoryRepository extends BaseRepository
{
    public function __construct(ProductCategory $model)
    {
        // Inject model ProductCategory ke BaseRepository
        parent::__construct($model);
    }

    // findAllWithProducts: Ambil semua kategori beserta daftar produknya (eager load).
    // Digunakan di endpoint listing kategori dengan detail produk.
    public function findAllWithProducts(): Collection
    {
        return $this->model->with('products')->get();
    }

    // findByName: Cari kategori berdasarkan nama (case-insensitive).
    // Berguna untuk validasi duplikasi sebelum create.
    public function findByName(string $name): ?ProductCategory
    {
        return $this->model->whereRaw('LOWER(category_name) = ?', [strtolower($name)])->first();
    }
}