<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya admin yang boleh membuat profil dokter baru
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id'           => 'required|exists:users,user_id|unique:doctors,user_id',
            'spec_id'           => 'required|exists:specializations,spec_id',
            'license_no'        => 'required|string|max:100|unique:doctors,license_no',
            'is_available'      => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'      => 'User wajib diisi.',
            'user_id.exists'        => 'User tidak ditemukan.',
            'user_id.unique'        => 'User ini sudah terdaftar sebagai dokter.',
            'spec_id.required'      => 'Spesialisasi wajib diisi.',
            'spec_id.exists'        => 'Spesialisasi tidak ditemukan.',
            'license_no.required'   => 'Nomor lisensi wajib diisi.',
            'license_no.unique'     => 'Nomor lisensi sudah digunakan.',
        ];
    }
}