<?php

namespace App\Http\Requests\Patient;

// Namespace disesuaikan dengan struktur folder di screenshot: app/Http/Requests/Patient/

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Diubah ke true agar request tidak selalu ditolak
        // TODO: tambahkan pengecekan kepemilikan jika perlu, misal: hanya pasien itu sendiri atau admin
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Semua field nullable karena ini partial update (PATCH-friendly)
            'date_of_birth'     => 'nullable|date',
            'gender'            => 'nullable|string|in:male,female,other',
            'blood_type'        => 'nullable|string|max:5',
            'address'           => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'gender.in' => 'Gender harus salah satu dari: male, female, other.',
        ];
    }
}