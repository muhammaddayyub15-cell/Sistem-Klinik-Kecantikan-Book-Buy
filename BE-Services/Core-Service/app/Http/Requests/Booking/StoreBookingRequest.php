<?php

namespace App\Http\Requests\Booking;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_id'     => 'required|exists:patients,patient_id',
            'doctor_id'      => 'required|exists:doctors,doctor_id',
            'doctsched_id'   => 'required|exists:doctors_schedules,doctsched_id',
            'service_id'     => 'required|exists:services,service_id',
            'booked_date'    => 'required|date|after_or_equal:today',
            'notes'          => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required'   => 'Pasien wajib diisi.',
            'patient_id.exists'     => 'Patient not found.',
            'doctor_id.required'    => 'Dokter wajib diisi.',
            'doctor_id.exists'      => 'Dokter tidak ditemukan.',
            'doctsched_id.required' => 'Jadwal dokter wajib diisi.',
            'doctsched_id.exists'   => 'Jadwal yang dipilih tidak ditemukan.',
            'service_id.required'   => 'Layanan wajib diisi.',
            'service_id.exists'     => 'Layanan tidak ditemukan.',
            'booked_date.required'  => 'Tanggal booking wajib diisi.',
            'booked_date.after_or_equal' => 'Tanggal booking tidak boleh dalam waktu yang lalu.',
        ];
    }
}