<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\MedicalRecord\MedicalRecord;
use App\Models\Booking\Booking;
use App\Models\Patient\Patient;
use App\Models\Doctor\Doctor;

class MedicalRecordsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ──────────────────────────────────────────────────────────────────
        // Hanya booking dengan status 'completed' yang memiliki medical record
        // booking_id 1 → record_id 1  (Dewi Kusuma   | dr. Sari Indah   | ada prescription)
        // booking_id 2 → record_id 2  (Anisa Putri   | dr. Budi Santoso | ada prescription)
        // booking_id 3 → record_id 3  (Rina Maharani | dr. Sari Indah   | tanpa prescription)
        //
        // booking_id 4 (confirmed) → TIDAK ada medical record
        // booking_id 5 (cancelled) → TIDAK ada medical record
        //
        // Catatan: booking_id di tabel medical_records bersifat UNIQUE
        // (satu booking hanya bisa punya satu medical record)
        // ──────────────────────────────────────────────────────────────────

        DB::table('medical_records')->insertOrIgnore([

            // ── Record 1 ──────────────────────────────────────────────────
            // Booking 1 | Dewi Kusuma | dr. Sari Indah | Facial Treatment
            [
                'record_id'         => 1,
                'booking_id'        => 1, // booking_id 1 — completed ✓
                'patient_id'        => 1, // Dewi Kusuma → konsisten dengan booking_id 1 ✓
                'doctor_id'         => 1, // dr. Sari Indah → konsisten dengan booking_id 1 ✓
                'diagnosis'         => 'Kulit kusam dengan hiperpigmentasi ringan di area pipi dan dahi. Kondisi kulit kering akibat kurangnya hidrasi dan paparan sinar UV.',
                'prescription_text' => 'Vitamin C Serum 20% — aplikasikan pagi hari setelah cuci muka. Hyaluronic Acid Booster — gunakan sebelum moisturiser pagi dan malam. Sunscreen SPF 50+ — wajib setiap pagi.',
                'recorded_at'       => Carbon::parse('2025-05-05 10:05:00'),
                'deleted_at'        => null,
                'created_at'        => Carbon::parse('2025-05-05 10:05:00'),
                'updated_at'        => Carbon::parse('2025-05-05 10:15:00'),
            ],

            // ── Record 2 ──────────────────────────────────────────────────
            // Booking 2 | Anisa Putri | dr. Budi Santoso | Laser Therapy
            [
                'record_id'         => 2,
                'booking_id'        => 2, // booking_id 2 — completed ✓
                'patient_id'        => 2, // Anisa Putri → konsisten dengan booking_id 2 ✓
                'doctor_id'         => 2, // dr. Budi Santoso → konsisten dengan booking_id 2 ✓
                'diagnosis'         => 'Post-acne hyperpigmentation grade II di area pipi kiri dan kanan. Tekstur kulit tidak merata akibat bekas jerawat lama. Pori-pori membesar.',
                'prescription_text' => 'Retinol Night Treatment 0.3% — gunakan malam hari 3x seminggu selama 4 minggu pertama. AHA BHA Exfoliating Toner — gunakan 2x seminggu untuk tekstur kulit. Calming Centella Cream — aplikasikan setelah laser untuk menenangkan kulit.',
                'recorded_at'       => Carbon::parse('2025-05-07 14:50:00'),
                'deleted_at'        => null,
                'created_at'        => Carbon::parse('2025-05-07 14:50:00'),
                'updated_at'        => Carbon::parse('2025-05-07 15:00:00'),
            ],

            // ── Record 3 ──────────────────────────────────────────────────
            // Booking 3 | Rina Maharani | dr. Sari Indah | Skin Consultation
            // Tanpa prescription karena hanya konsultasi awal
            [
                'record_id'         => 3,
                'booking_id'        => 3, // booking_id 3 — completed ✓
                'patient_id'        => 3, // Rina Maharani → konsisten dengan booking_id 3 ✓
                'doctor_id'         => 1, // dr. Sari Indah → konsisten dengan booking_id 3 ✓
                'diagnosis'         => 'Kulit sensitif dengan kecenderungan kemerahan (rosacea ringan). Skin barrier lemah akibat pemakaian produk yang tidak sesuai jenis kulit. Disarankan untuk memulai dengan skincare dasar yang gentle.',
                'prescription_text' => null, // Konsultasi awal — resep belum diberikan, pasien diminta kembali 2 minggu lagi
                'recorded_at'       => Carbon::parse('2025-05-09 10:20:00'),
                'deleted_at'        => null,
                'created_at'        => Carbon::parse('2025-05-09 10:20:00'),
                'updated_at'        => Carbon::parse('2025-05-09 10:30:00'),
            ],
        ]);
    }
}