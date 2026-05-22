<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductCategoryRequest;
use App\Service\ProductCategoryService;
use App\Service\Shared\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// ProductCategoryController: Menangani endpoint untuk manajemen kategori produk.
class ProductCategoryController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private ProductCategoryService $categoryService)
    {
    }

    // index: GET /product-categories
    // Query param: ?with_products=true untuk eager load produk dalam setiap kategori
    public function index(Request $request): JsonResponse
    {
        $withProducts = $request->boolean('with_products', false);
        $categories   = $this->categoryService->getAllCategories($withProducts);
        return $this->successResponse($categories, 'Daftar kategori berhasil diambil');
    }

    // show: GET /product-categories/{id}
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);
        return $this->successResponse($category, 'Detail kategori berhasil diambil');
    }

    // store: POST /product-categories
    public function store(StoreProductCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());
        return $this->createdResponse($category, 'Kategori berhasil dibuat');
    }

    // update: PUT/PATCH /product-categories/{id}
    // Menggunakan FormRequest generik karena validasi update kategori sederhana
    public function update(Request $request, int $id): JsonResponse
    {
        // Validasi inline untuk update (field opsional)
        $data = $request->validate([
            'category_name' => "sometimes|string|max:255|unique:product_categories,category_name,{$id}",
        ]);
        $category = $this->categoryService->updateCategory($id, $data);
        return $this->successResponse($category, 'Kategori berhasil diperbarui');
    }

    // destroy: DELETE /product-categories/{id}
    // Gagal jika kategori masih memiliki produk aktif (cek di ProductCategoryService).
    public function destroy(int $id): JsonResponse
    {
        $this->categoryService->deleteCategory($id);
        return $this->successResponse(null, 'Kategori berhasil dihapus');
    }
}