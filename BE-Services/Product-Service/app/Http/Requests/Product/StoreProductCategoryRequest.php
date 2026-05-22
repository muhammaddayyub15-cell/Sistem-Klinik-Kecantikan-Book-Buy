<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

// StoreProductCategoryRequest: Validasi request saat membuat kategori produk baru.
class StoreProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_name' => 'required|string|max:255|unique:product_categories,category_name',
        ];
    }

    public function messages(): array
    {
        return [
            'category_name.unique' => 'Nama kategori sudah ada.',
        ];
    }
}