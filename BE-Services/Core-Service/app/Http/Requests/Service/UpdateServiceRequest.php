<?php

namespace App\Http\Requests\Service;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: batasi hanya role admin/dokter yang bisa update layanan
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Semua nullable untuk mendukung partial update
            'category_id'  => 'nullable|exists:services_categories,category_id',
            'service_name' => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'base_price'   => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'Kategori layanan tidak ditemukan.',
            'base_price.numeric' => 'Harga dasar harus berupa angka.',
            'base_price.min'     => 'Harga dasar tidak boleh negatif.',
        ];
    }
}