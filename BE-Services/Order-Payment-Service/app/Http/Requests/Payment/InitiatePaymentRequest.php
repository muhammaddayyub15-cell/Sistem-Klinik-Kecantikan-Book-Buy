<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

// InitiatePaymentRequest: Validasi request saat inisiasi pembayaran (POST /payments/initiate).
// Request ini sederhana — hanya butuh order_id karena semua data pembayaran
// diambil dari order yang sudah ada di database.
class InitiatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => 'required|integer|exists:orders,id',
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.exists' => 'Order tidak ditemukan.',
        ];
    }
}