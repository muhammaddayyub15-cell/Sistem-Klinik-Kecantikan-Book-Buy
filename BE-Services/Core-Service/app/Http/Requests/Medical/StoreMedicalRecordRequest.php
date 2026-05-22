<?php

namespace App\Http\Requests\Medical;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya dokter yang boleh membuat rekam medis
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_id'        => 'required|exists:bookings,booking_id|unique:medical_records,booking_id',
            'patient_id'        => 'required|exists:patients,patient_id',
            'doctor_id'         => 'required|exists:doctors,doctor_id',
            'diagnosis'         => 'required|string|max:1000',
            'prescription_text' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required'   => 'Booking wajib diisi.',
            'booking_id.exists'     => 'Booking tidak ditemukan.',
            'booking_id.unique'     => 'Rekam medis untuk booking ini sudah ada.',
            'patient_id.required'   => 'Pasien wajib diisi.',
            'patient_id.exists'     => 'Pasien tidak ditemukan.',
            'doctor_id.required'    => 'Dokter wajib diisi.',
            'doctor_id.exists'      => 'Dokter tidak ditemukan.',
            'diagnosis.required'    => 'Diagnosis wajib diisi.',
        ];
    }
}