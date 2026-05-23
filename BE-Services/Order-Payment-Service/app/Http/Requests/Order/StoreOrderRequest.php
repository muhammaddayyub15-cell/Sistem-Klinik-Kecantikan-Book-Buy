<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

// StoreOrderRequest: Validasi request saat membuat order baru (POST /orders).
//
// CATATAN ARSITEKTUR:
// Data pasien (patient_id_snapshot, patient_name_snapshot) dan produk
// (product_id_snapshot, product_name_snapshot, unit_price_snapshot) dikirim
// dari client setelah mengambil data dari Core Service dan Product Service.
// FormRequest ini hanya memvalidasi format data, bukan keberadaan data di service lain.
// Validasi keberadaan data di service lain (apakah patient_id valid, apakah produk ada)
// sebaiknya dilakukan di Controller sebelum memanggil OrderService.
class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id_snapshot'    => 'required|integer',
            'patient_name_snapshot'  => 'required|string|max:255',
            // booking_id_snapshot opsional — order bisa dibuat tanpa booking
            'booking_id_snapshot'    => 'nullable|integer',
            // items: minimal harus ada satu produk dalam order
            'items'                  => 'required|array|min:1',
            'items.*.product_id_snapshot'   => 'required|integer',
            'items.*.product_name_snapshot' => 'required|string|max:255',
            'items.*.unit_price_snapshot'   => 'required|numeric|min:0',
            'items.*.qty'                   => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'                      => 'Order harus memiliki minimal satu item.',
            'items.*.qty.min'                     => 'Jumlah item tidak boleh kurang dari 1.',
            'items.*.unit_price_snapshot.min'     => 'Harga produk tidak boleh negatif.',
        ];
    }
}