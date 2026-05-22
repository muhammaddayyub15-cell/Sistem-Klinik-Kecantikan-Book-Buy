<?php

namespace App\Service;

use App\Service\Repositories\ProductCategoryRepository;
use App\Service\Shared\Base\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

// ProductCategoryService: Business logic untuk manajemen kategori produk.
// Controller tidak boleh akses repository langsung — semua lewat service ini.
class ProductCategoryService extends BaseService
{
    // Simpan reference ke typed repository untuk akses method spesifik
    private ProductCategoryRepository $categoryRepository;

    public function __construct(ProductCategoryRepository $repository)
    {
        parent::__construct($repository);
        $this->categoryRepository = $repository;
    }

    // getAllCategories: Ambil semua kategori.
    // $withProducts: jika true, eager load relasi products sekaligus.
    public function getAllCategories(bool $withProducts = false): Collection
    {
        if ($withProducts) {
            return $this->categoryRepository->findAllWithProducts();
        }
        return $this->categoryRepository->findAll();
    }

    // getCategoryById: Ambil satu kategori berdasarkan ID.
    public function getCategoryById(int $id): Model
    {
        return $this->categoryRepository->findById($id);
    }

    // createCategory: Buat kategori baru.
    // Validasi duplikasi nama dilakukan di sini, bukan di controller.
    public function createCategory(array $data): Model
    {
        // Cek apakah kategori dengan nama yang sama sudah ada
        $existing = $this->categoryRepository->findByName($data['category_name']);
        if ($existing) {
            // Lempar exception agar bisa ditangkap di controller
            throw new \Exception('Kategori dengan nama tersebut sudah ada.', 422);
        }

        return $this->categoryRepository->create($data);
    }

    // updateCategory: Update data kategori.
    public function updateCategory(int $id, array $data): Model
    {
        return $this->categoryRepository->update($id, $data);
    }

    // deleteCategory: Soft delete kategori.
    // PERHATIAN: Tidak bisa hapus kategori yang masih memiliki produk aktif.
    public function deleteCategory(int $id): bool
    {
        $category = $this->categoryRepository->findById($id, ['products']);

        if ($category->products->count() > 0) {
            throw new \Exception('Kategori tidak dapat dihapus karena masih memiliki produk.', 422);
        }

        return $this->categoryRepository->delete($id);
    }
}