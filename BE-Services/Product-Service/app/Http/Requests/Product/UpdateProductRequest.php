<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

// UpdateProductRequest: Validasi request saat update produk (PUT/PATCH /products/{id}).
// Semua field bersifat opsional (sometimes) karena mendukung partial update.
class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Ambil ID produk dari route parameter untuk validasi unique SKU
        // (SKU boleh sama dengan milik produk itu sendiri, tapi tidak boleh sama dengan produk lain)
        $productId = $this->route('id');

        return [
            'product_name' => 'sometimes|string|max:255',
            // Ignore unique check untuk produk yang sedang diupdate
            'SKU'          => "sometimes|string|max:100|unique:products,SKU,{$productId}",
            'category_id'  => 'sometimes|integer|exists:product_categories,id',
            'price'        => 'sometimes|numeric|min:0',
            'unit'         => 'sometimes|string|max:50',
            // CATATAN: stock_qty tidak diupdate langsung di sini.
            // Perubahan stok harus melalui endpoint /products/{id}/stock
            // agar StockLog tercatat dengan benar.
        ];
    }

    public function messages(): array
    {
        return [
            'SKU.unique'         => 'SKU sudah digunakan oleh produk lain.',
            'category_id.exists' => 'Kategori produk tidak ditemukan.',
        ];
    }
}