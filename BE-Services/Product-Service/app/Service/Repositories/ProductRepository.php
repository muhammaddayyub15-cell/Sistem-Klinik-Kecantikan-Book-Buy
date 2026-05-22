<?php

namespace App\Service\Repositories;

use App\Models\Product\Product;
use App\Service\Shared\Base\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

// ProductRepository: Menangani semua query ke tabel products.
class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    // findAllPaginated: Ambil produk dengan pagination dan eager load kategori.
    // $perPage default 15, bisa dikonfigurasi dari request query ?per_page=
    public function findAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('category')->paginate($perPage);
    }

    // findByCategoryId: Filter produk berdasarkan kategori.
    public function findByCategoryId(int $categoryId): Collection
    {
        return $this->model->with('category')
            ->where('category_id', $categoryId)
            ->get();
    }

    // findBySKU: Cari produk berdasarkan SKU unik.
    // Digunakan untuk validasi duplikasi SKU saat create/update.
    public function findBySKU(string $sku): ?Product
    {
        return $this->model->where('SKU', $sku)->first();
    }

    // updateStock: Update kolom stock_qty langsung (increment/decrement).
    // $qty positif = tambah stok, negatif = kurangi stok.
    // PENTING: Method ini tidak membuat StockLog — itu tanggung jawab ProductService.
    public function updateStock(int $productId, int $qty): Product
    {
        $product = $this->findById($productId);
        $product->increment('stock_qty', $qty);
        return $product->fresh();
    }
}