<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

// StoreSnapshotRequest: Validasi request saat service lain mengirim snapshot (POST /reports/snapshot).
// Dikirim oleh Core Service, Product Service, atau Order Service setelah event tertentu.
class StoreSnapshotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // report_type: jenis laporan yang dikirim oleh service pengirim
            'report_type'  => 'required|in:booking_stats,revenue_stats,product_stats,doctor_performance',
            // period_start dan period_end: rentang waktu data yang dirangkum dalam snapshot ini
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after_or_equal:period_start',
            // data: payload agregat bebas format — disimpan sebagai JSON
            'data'         => 'required|array',
        ];
    }

    public function messages(): array
    {
        return [
            'report_type.in'               => 'Jenis laporan tidak valid. Pilihan: booking_stats, revenue_stats, product_stats, doctor_performance.',
            'period_end.after_or_equal'    => 'Tanggal akhir periode tidak boleh sebelum tanggal mulai.',
            'data.required'                => 'Data snapshot tidak boleh kosong.',
        ];
    }
}