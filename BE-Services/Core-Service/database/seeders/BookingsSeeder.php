<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Booking\Booking;
use App\Models\Doctor\DoctorSchedule;
use App\Models\Service\Service;
use App\Models\Patient\Patient;
use App\Models\Doctor\Doctor;
use App\Models\MedicalRecord\MedicalRecord;
use App\Models\MedicalRecord\Prescription;

class BookingsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ──────────────────────────────────────────────────────────────────
        // REFERENSI DOKTER & JADWAL (dari DoctorSeeder & DoctorScheduleSeeder)
        // ──────────────────────────────────────────────────────────────────
        // doctor_id 1 = dr. Sari Indah   → schedule_id 1–5  (Senin–Jumat 08:00–12:00)
        // doctor_id 2 = dr. Budi Santoso → schedule_id 6–10 (Selasa–Sabtu 13:00–17:00)
        //                                   schedule_id 10   (Sabtu 09:00–13:00)
        //
        // REFERENSI PASIEN (dari PatientSeeder)
        // patient_id 1 = Dewi Kusuma
        // patient_id 2 = Anisa Putri
        // patient_id 3 = Rina Maharani
        //
        // REFERENSI LAYANAN (dari ServiceSeeder)
        // service_id 1 = Facial Treatment        (Rp 350.000)
        // service_id 2 = Brightening Facial      (Rp 420.000)
        // service_id 3 = Laser Therapy           (Rp 750.000)
        // service_id 5 = Botox Injection         (Rp 1.200.000)
        // service_id 7 = Skin Consultation       (Rp 0)
        //
        // DISTRIBUSI STATUS:
        // booking_id 1 → completed  (ada medical_record + prescription)
        // booking_id 2 → completed  (ada medical_record + prescription)
        // booking_id 3 → completed  (ada medical_record, tanpa prescription)
        // booking_id 4 → confirmed  (belum ada medical_record)
        // booking_id 5 → cancelled
        // ──────────────────────────────────────────────────────────────────

        DB::table('bookings')->insertOrIgnore([

            // ── Booking 1 — COMPLETED ─────────────────────────────────────
            // Dewi Kusuma | dr. Sari Indah | Senin 2025-05-05 | Facial Treatment
            // schedule_id 1 = doctor_id 1, Monday 08:00–12:00 ✓
            [
                'booking_id'         => 1,
                'patient_id'         => 1, // Dewi Kusuma
                'doctor_id'          => 1, // dr. Sari Indah
                'doctor_schedule_id' => 1, // Monday 08:00–12:00 → doctor_id 1 ✓
                'service_id'         => 1, // Facial Treatment
                'booked_date'        => '2025-05-05', // Senin
                'start_time'         => '09:00:00',
                'end_time'           => '10:00:00',
                'status'             => 'completed',
                'notes'              => 'Pasien ingin perawatan wajah rutin bulanan.',
                'deleted_at'         => null,
                'created_at'         => Carbon::parse('2025-05-01 10:00:00'),
                'updated_at'         => Carbon::parse('2025-05-05 10:15:00'),
            ],

            // ── Booking 2 — COMPLETED ─────────────────────────────────────
            // Anisa Putri | dr. Budi Santoso | Rabu 2025-05-07 | Laser Therapy
            // schedule_id 7 = doctor_id 2, Wednesday 13:00–17:00 ✓
            [
                'booking_id'         => 2,
                'patient_id'         => 2, // Anisa Putri
                'doctor_id'          => 2, // dr. Budi Santoso
                'doctor_schedule_id' => 7, // Wednesday 13:00–17:00 → doctor_id 2 ✓
                'service_id'         => 3, // Laser Therapy
                'booked_date'        => '2025-05-07', // Rabu
                'start_time'         => '14:00:00',
                'end_time'           => '14:45:00',
                'status'             => 'completed',
                'notes'              => 'Terapi laser untuk bekas jerawat di area pipi.',
                'deleted_at'         => null,
                'created_at'         => Carbon::parse('2025-05-03 14:00:00'),
                'updated_at'         => Carbon::parse('2025-05-07 15:00:00'),
            ],

            // ── Booking 3 — COMPLETED (tanpa prescription) ───────────────
            // Rina Maharani | dr. Sari Indah | Jumat 2025-05-09 | Skin Consultation
            // schedule_id 5 = doctor_id 1, Friday 08:00–12:00 ✓
            [
                'booking_id'         => 3,
                'patient_id'         => 3, // Rina Maharani
                'doctor_id'          => 1, // dr. Sari Indah
                'doctor_schedule_id' => 5, // Friday 08:00–12:00 → doctor_id 1 ✓
                'service_id'         => 7, // Skin Consultation
                'booked_date'        => '2025-05-09', // Jumat
                'start_time'         => '10:00:00',
                'end_time'           => '10:30:00',
                'status'             => 'completed',
                'notes'              => 'Konsultasi pertama, pasien baru.',
                'deleted_at'         => null,
                'created_at'         => Carbon::parse('2025-05-06 09:00:00'),
                'updated_at'         => Carbon::parse('2025-05-09 10:35:00'),
            ],

            // ── Booking 4 — CONFIRMED (mendatang, belum ada medical record) ─
            // Dewi Kusuma | dr. Budi Santoso | Sabtu 2025-06-07 | Botox Injection
            // schedule_id 10 = doctor_id 2, Saturday 09:00–13:00 ✓
            [
                'booking_id'         => 4,
                'patient_id'         => 1, // Dewi Kusuma
                'doctor_id'          => 2, // dr. Budi Santoso
                'doctor_schedule_id' => 10, // Saturday 09:00–13:00 → doctor_id 2 ✓
                'service_id'         => 5,  // Botox Injection
                'booked_date'        => '2025-06-07', // Sabtu (mendatang)
                'start_time'         => '10:00:00',
                'end_time'           => '10:30:00',
                'status'             => 'confirmed',
                'notes'              => 'Pasien sudah pernah melakukan botox sebelumnya.',
                'deleted_at'         => null,
                'created_at'         => Carbon::parse('2025-05-20 11:00:00'),
                'updated_at'         => Carbon::parse('2025-05-20 11:30:00'),
            ],

            // ── Booking 5 — CANCELLED ─────────────────────────────────────
            // Anisa Putri | dr. Sari Indah | Selasa 2025-05-13 | Brightening Facial
            // schedule_id 2 = doctor_id 1, Tuesday 08:00–12:00 ✓
            [
                'booking_id'         => 5,
                'patient_id'         => 2, // Anisa Putri
                'doctor_id'          => 1, // dr. Sari Indah
                'doctor_schedule_id' => 2, // Tuesday 08:00–12:00 → doctor_id 1 ✓
                'service_id'         => 2, // Brightening Facial
                'booked_date'        => '2025-05-13', // Selasa
                'start_time'         => '09:00:00',
                'end_time'           => '10:15:00',
                'status'             => 'cancelled',
                'notes'              => 'Pasien membatalkan karena ada keperluan mendadak.',
                'deleted_at'         => null,
                'created_at'         => Carbon::parse('2025-05-08 08:00:00'),
                'updated_at'         => Carbon::parse('2025-05-12 16:00:00'),
            ],
        ]);
    }
}