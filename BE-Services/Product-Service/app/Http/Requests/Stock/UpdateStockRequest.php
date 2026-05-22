<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

// UpdateStockRequest: Validasi request saat mengubah stok produk (POST /products/{id}/stock).
// Dipisah dari UpdateProductRequest agar perubahan stok selalu disertai data log yang lengkap.
class UpdateStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // change_qty: jumlah perubahan stok.
            // Boleh negatif untuk pengurangan stok, tapi validasi min stok ada di ProductService.
            'change_qty'   => 'required|integer|not_in:0',
            // type: jenis perubahan stok
            'type'         => 'required|in:in,out,adjustment',
            // reference_id: opsional, ID dari sistem eksternal (misal order_id dari Order Service)
            'reference_id' => 'nullable|integer',
            'notes'        => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'change_qty.not_in' => 'Jumlah perubahan stok tidak boleh nol.',
            'type.in'           => 'Tipe perubahan stok harus salah satu dari: in, out, adjustment.',
        ];
    }
}