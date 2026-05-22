<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

// StoreProductRequest: Validasi request saat membuat produk baru (POST /products).
// Semua aturan validasi terpusat di sini, bukan di controller.
class StoreProductRequest extends FormRequest
{
    // authorize: Izin akses dikontrol oleh RoleMiddleware di route, bukan di sini.
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_name' => 'required|string|max:255',
            // SKU harus unik di tabel products — validasi duplikasi juga ada di ProductService
            'SKU'          => 'required|string|max:100|unique:products,SKU',
            'category_id'  => 'required|integer|exists:product_categories,id',
            'price'        => 'required|numeric|min:0',
            // stock_qty boleh kosong saat create, default ke 0 jika tidak diisi
            'stock_qty'    => 'nullable|integer|min:0',
            'unit'         => 'required|string|max:50', // contoh: pcs, box, tablet, ml
        ];
    }

    public function messages(): array
    {
        return [
            'SKU.unique'          => 'SKU sudah digunakan oleh produk lain.',
            'category_id.exists'  => 'Kategori produk tidak ditemukan.',
            'price.min'           => 'Harga tidak boleh negatif.',
        ];
    }
}