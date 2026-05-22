<?php

namespace App\Service;

use App\Service\Repositories\ProductRepository;
use App\Service\Repositories\StockLogRepository;
use App\Service\Shared\Base\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

// ProductService: Business logic untuk manajemen produk dan stok.
// Mengelola dua repository sekaligus: ProductRepository & StockLogRepository.
// Setiap perubahan stok (updateStock) wajib diikuti pencatatan di StockLog.
class ProductService extends BaseService
{
    private ProductRepository $productRepository;
    private StockLogRepository $stockLogRepository;

    public function __construct(
        ProductRepository $productRepository,
        StockLogRepository $stockLogRepository
    ) {
        // BaseService hanya butuh satu repository — pass productRepository sebagai primary
        parent::__construct($productRepository);
        $this->productRepository  = $productRepository;
        $this->stockLogRepository = $stockLogRepository;
    }

    // getAllProducts: Ambil semua produk dengan pagination.
    public function getAllProducts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->findAllPaginated($perPage);
    }

    // getProductById: Ambil satu produk berdasarkan ID.
    public function getProductById(int $id): Model
    {
        return $this->productRepository->findById($id, ['category']);
    }

    // createProduct: Buat produk baru.
    // Validasi SKU duplikat dilakukan sebelum insert.
    public function createProduct(array $data): Model
    {
        $existing = $this->productRepository->findBySKU($data['SKU']);
        if ($existing) {
            throw new \Exception('SKU sudah digunakan oleh produk lain.', 422);
        }

        $product = $this->productRepository->create($data);

        // Catat log stok awal jika ada stock_qty pada saat produk dibuat
        if (!empty($data['stock_qty']) && $data['stock_qty'] > 0) {
            $this->stockLogRepository->createLog([
                'product_id' => $product->id,
                'change_qty' => $data['stock_qty'],
                'type'       => 'in',
                'notes'      => 'Stok awal saat produk dibuat',
            ]);
        }

        return $product;
    }

    // updateProduct: Update data produk (bukan stok — gunakan updateStock untuk itu).
    public function updateProduct(int $id, array $data): Model
    {
        // Jika SKU diubah, validasi duplikasi SKU baru
        if (!empty($data['SKU'])) {
            $existing = $this->productRepository->findBySKU($data['SKU']);
            if ($existing && $existing->id !== $id) {
                throw new \Exception('SKU sudah digunakan oleh produk lain.', 422);
            }
        }

        return $this->productRepository->update($id, $data);
    }

    // deleteProduct: Soft delete produk.
    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->delete($id);
    }

    // updateStock: Update stok produk DAN catat log perubahan secara atomik.
    // $qty: jumlah perubahan (positif = masuk, negatif = keluar)
    // $type: 'in' | 'out' | 'adjustment'
    // $referenceId: opsional, ID dari sistem lain (misal order_id dari Order Service)
    //
    // PENTING: Validasi stok tidak boleh negatif dilakukan di sini.
    public function updateStock(int $productId, int $qty, string $type, ?int $referenceId = null, ?string $notes = null): Model
    {
        $product = $this->productRepository->findById($productId);

        // Cegah stok menjadi negatif saat pengurangan
        if ($type === 'out' && ($product->stock_qty + $qty) < 0) {
            throw new \Exception('Stok tidak mencukupi untuk dikurangi.', 422);
        }

        // Update stok di tabel products
        $updatedProduct = $this->productRepository->updateStock($productId, $qty);

        // Catat log perubahan stok — WAJIB dilakukan setiap ada perubahan stok
        $this->stockLogRepository->createLog([
            'product_id'   => $productId,
            'change_qty'   => $qty,
            'type'         => $type,
            'reference_id' => $referenceId,
            'notes'        => $notes,
        ]);

        return $updatedProduct;
    }

    // getStockLogs: Ambil riwayat stok untuk satu produk.
    public function getStockLogs(int $productId): \Illuminate\Database\Eloquent\Collection
    {
        // Validasi produk ada terlebih dahulu
        $this->productRepository->findById($productId);
        return $this->stockLogRepository->findByProductId($productId);
    }
}