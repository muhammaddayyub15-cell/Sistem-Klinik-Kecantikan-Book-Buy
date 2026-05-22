<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya admin yang boleh mengubah profil dokter
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Ambil doctor_id dari route parameter untuk pengecualian unique check
        $doctorId = $this->route('id');

        return [
            'spec_id'       => 'nullable|exists:specializations,spec_id',
            'license_no'    => 'nullable|string|max:100|unique:doctors,license_no,' . $doctorId . ',doctor_id',
            'is_available'  => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'spec_id.exists'        => 'Spesialisasi tidak ditemukan.',
            'license_no.unique'     => 'Nomor lisensi sudah digunakan oleh dokter lain.',
        ];
    }
}