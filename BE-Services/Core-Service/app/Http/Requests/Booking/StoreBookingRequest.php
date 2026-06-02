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
            'patient_id'  => 'required|exists:patients,patient_id',
            'doctor_id'   => 'required|exists:doctors,doctor_id',
            'service_id'  => 'required|exists:services,service_id',
            'booked_date' => 'required|date|after_or_equal:today',
            'notes'       => 'nullable|string|max:500',

            // ✅ nullable — tidak wajib dikirim frontend
            // backend yang set otomatis di BookingService::createBooking()
            // kolom di tabel adalah schedule_id, bukan doctor_schedule_id
            'doctor_schedule_id' => 'nullable|exists:doctor_schedules,schedule_id',
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required'        => 'Pasien wajib diisi.',
            'patient_id.exists'          => 'Pasien tidak ditemukan.',
            'doctor_id.required'         => 'Dokter wajib diisi.',
            'doctor_id.exists'           => 'Dokter tidak ditemukan.',
            'service_id.required'        => 'Layanan wajib diisi.',
            'service_id.exists'          => 'Layanan tidak ditemukan.',
            'booked_date.required'       => 'Tanggal booking wajib diisi.',
            'booked_date.after_or_equal' => 'Tanggal booking tidak boleh dalam waktu yang lalu.',
        ];
    }
}