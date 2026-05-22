<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\Product\UpdateStockRequest;
use App\Service\ProductService;
use App\Service\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// ProductController: Thin controller — hanya menerima request, memanggil service,
// dan mengembalikan response. Tidak ada business logic di sini.
class ProductController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private ProductService $productService)
    {
        // ProductService di-inject via constructor (IoC Container Laravel)
    }

    // index: GET /products
    // Mengembalikan daftar produk dengan pagination.
    // Query param: ?per_page=15
    public function index(Request $request): JsonResponse
    {
        $perPage  = $request->query('per_page', 15);
        $products = $this->productService->getAllProducts((int) $perPage);
        return $this->successResponse($products, 'Daftar produk berhasil diambil');
    }

    // show: GET /products/{id}
    // Mengembalikan detail satu produk beserta kategorinya.
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);
        return $this->successResponse($product, 'Detail produk berhasil diambil');
    }

    // store: POST /products
    // Membuat produk baru. Hanya bisa diakses oleh role admin (diatur di route middleware).
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());
        return $this->createdResponse($product, 'Produk berhasil dibuat');
    }

    // update: PUT/PATCH /products/{id}
    // Update data produk (bukan stok). Gunakan endpoint /stock untuk update stok.
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->updateProduct($id, $request->validated());
        return $this->successResponse($product, 'Produk berhasil diperbarui');
    }

    // destroy: DELETE /products/{id}
    // Soft delete produk.
    public function destroy(int $id): JsonResponse
    {
        $this->productService->deleteProduct($id);
        return $this->successResponse(null, 'Produk berhasil dihapus');
    }

    // updateStock: POST /products/{id}/stock
    // Endpoint khusus untuk update stok. Dipisah agar perubahan stok selalu tercatat di StockLog.
    // Bisa dipanggil oleh internal service (misal Order Service via API call) setelah order selesai.
    public function updateStock(UpdateStockRequest $request, int $id): JsonResponse
    {
        $data    = $request->validated();
        $product = $this->productService->updateStock(
            productId:   $id,
            qty:         $data['change_qty'],
            type:        $data['type'],
            referenceId: $data['reference_id'] ?? null,
            notes:       $data['notes'] ?? null,
        );
        return $this->successResponse($product, 'Stok produk berhasil diperbarui');
    }

    // stockLogs: GET /products/{id}/stock-logs
    // Ambil riwayat perubahan stok untuk satu produk.
    public function stockLogs(int $id): JsonResponse
    {
        $logs = $this->productService->getStockLogs($id);
        return $this->successResponse($logs, 'Riwayat stok berhasil diambil');
    }
}