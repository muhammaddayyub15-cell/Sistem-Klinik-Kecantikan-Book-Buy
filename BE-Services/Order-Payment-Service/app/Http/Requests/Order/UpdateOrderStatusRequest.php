<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

// UpdateOrderStatusRequest: Validasi request saat update status order manual (PATCH /orders/{id}/status).
// Digunakan oleh admin untuk update status order secara manual jika diperlukan.
class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Status yang diizinkan untuk diupdate manual oleh admin
            'status' => 'required|in:pending,processing,completed,cancelled',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status tidak valid. Pilihan: pending, processing, completed, cancelled.',
        ];
    }
}