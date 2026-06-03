<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id'         => 'required|integer',
            // booking_id_snapshot opsional — order bisa dibuat tanpa booking
            'booking_id'         => 'nullable|integer',
            // items: minimal harus ada satu produk dalam order
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.qty'        => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'       => 'Order harus memiliki minimal satu item.',
            'items.*.qty.min'      => 'Jumlah item tidak boleh kurang dari 1.',
            'patient_id.required'  => 'Patient ID wajib diisi.',
        ];
    }
}