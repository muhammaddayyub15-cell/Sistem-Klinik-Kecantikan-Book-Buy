<?php

namespace App\Http\Requests\Medical;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya dokter yang boleh menambahkan resep
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Validasi array resep — minimal 1 item wajib ada
            'prescriptions'                         => 'required|array|min:1',
            'prescriptions.*.product_id'            => 'required|integer',
            'prescriptions.*.product_name'          => 'required|string|max:255',
            'prescriptions.*.qty'                   => 'required|integer|min:1',
            'prescriptions.*.dosage_instruction'    => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'prescriptions.required'                        => 'Minimal satu resep wajib diisi.',
            'prescriptions.*.product_id.required'           => 'ID produk wajib diisi.',
            'prescriptions.*.product_name.required'         => 'Nama produk wajib diisi.',
            'prescriptions.*.qty.required'                  => 'Jumlah produk wajib diisi.',
            'prescriptions.*.qty.min'                       => 'Jumlah produk minimal 1.',
            'prescriptions.*.dosage_instruction.required'   => 'Instruksi dosis wajib diisi.',
        ];
    }
}