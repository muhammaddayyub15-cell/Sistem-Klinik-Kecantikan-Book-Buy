<?php

namespace App\Http\Requests\Patient;

// Namespace disesuaikan dengan struktur folder di screenshot: app/Http/Requests/Patient/

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Diubah ke true agar request tidak selalu ditolak
        // TODO: ganti dengan logika role check jika perlu, misal: auth()->check()
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // user_id wajib ada, harus valid di tabel users, dan belum punya profil pasien
            'user_id'     => 'required|exists:users,user_id|unique:patients,user_id',

            // date_of_birth 
            'date_of_birth'         => 'required|date',

            'gender'      => 'required|string|in:male,female,other',

            // blood_type sesuai ERD
            'blood_type'  => 'nullable|string|max:5',

            'address'     => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID wajib diisi.',
            'user_id.exists'   => 'User tidak ditemukan.',
            'user_id.unique'   => 'User ini sudah memiliki profil pasien.',
            'date_of_birth.required'     => 'Tanggal lahir wajib diisi.',
            'gender.in'        => 'Gender harus salah satu dari: male, female, other.',
        ];
    }
}