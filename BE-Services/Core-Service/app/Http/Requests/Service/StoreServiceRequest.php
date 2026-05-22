<?php

namespace App\Http\Requests\Service;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: batasi hanya role admin/dokter yang bisa tambah layanan
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Sesuai ERD: FK ke services_categories.category_id
            'category_id'  => 'required|exists:services_categories,category_id',

            // Sesuai ERD: kolom service_name (bukan name)
            'service_name' => 'required|string|max:255',

            'description'  => 'nullable|string',

            // Sesuai ERD: kolom base_price (bukan price)
            'base_price'   => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required'  => 'Kategori layanan wajib dipilih.',
            'category_id.exists'    => 'Kategori layanan tidak ditemukan.',
            'service_name.required' => 'Nama layanan wajib diisi.',
            'base_price.required'   => 'Harga dasar layanan wajib diisi.',
            'base_price.numeric'    => 'Harga dasar harus berupa angka.',
            'base_price.min'        => 'Harga dasar tidak boleh negatif.',
        ];
    }
}