<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Prescription\Prescription;
use App\Models\MedicalRecord\MedicalRecord;
use App\Models\Product\Product;
use App\Models\Booking\Booking;
use App\Models\Patient\Patient;
use App\Models\Doctor\Doctor;

class PrescriptionsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ──────────────────────────────────────────────────────────────────
        // Hanya record yang memiliki prescription_text yang dibuatkan resep detail
        // record_id 1 → Dewi Kusuma   (booking 1 | Facial Treatment)
        // record_id 2 → Anisa Putri   (booking 2 | Laser Therapy)
        // record_id 3 → Rina Maharani (booking 3 | Skin Consultation) → TIDAK ada resep
        //
        // product_id & product_name_snapshot referensi ProductSeeder:
        // product_id 1 = Brightening Vitamin C Serum  (Rp 385.000)
        // product_id 2 = Barrier Repair Moisturiser   (Rp 295.000)
        // product_id 3 = Invisible SPF 50+ Sunscreen  (Rp 245.000)
        // product_id 4 = Retinol Night Treatment      (Rp 520.000)
        // product_id 6 = Hyaluronic Acid Booster      (Rp 310.000)
        // product_id 7 = AHA BHA Exfoliating Toner    (Rp 265.000)
        // product_id 8 = Calming Centella Cream       (Rp 230.000)
        //
        // Catatan: product_name_snapshot disimpan sebagai snapshot nama produk
        // saat resep dibuat — tidak akan berubah meskipun nama produk diupdate
        // ──────────────────────────────────────────────────────────────────

        DB::table('prescriptions')->insertOrIgnore([

            // ── Resep untuk Record 1 (Dewi Kusuma) ────────────────────────
            // 3 item: Vitamin C Serum, Hyaluronic Acid Booster, Sunscreen

            [
                'presc_id'             => 1,
                'record_id'            => 1, // Medical Record Dewi Kusuma ✓
                'product_id'           => 1, // Brightening Vitamin C Serum
                'product_name_snapshot'=> 'Brightening Vitamin C Serum',
                'qty'                  => 1,
                'dosage_instruction'   => 'Aplikasikan 3-4 tetes pada wajah setiap pagi setelah membersihkan wajah. Tunggu 1-2 menit sebelum melanjutkan rutinitas skincare.',
                'prescribed_at'        => Carbon::parse('2025-05-05 10:10:00'),
                'created_at'           => Carbon::parse('2025-05-05 10:10:00'),
                'updated_at'           => Carbon::parse('2025-05-05 10:10:00'),
            ],
            [
                'presc_id'             => 2,
                'record_id'            => 1, // Medical Record Dewi Kusuma ✓
                'product_id'           => 6, // Hyaluronic Acid Booster
                'product_name_snapshot'=> 'Hyaluronic Acid Booster',
                'qty'                  => 1,
                'dosage_instruction'   => 'Gunakan 4-5 tetes pada kulit yang sedikit lembap, pagi dan malam hari sebelum moisturiser.',
                'prescribed_at'        => Carbon::parse('2025-05-05 10:10:00'),
                'created_at'           => Carbon::parse('2025-05-05 10:10:00'),
                'updated_at'           => Carbon::parse('2025-05-05 10:10:00'),
            ],
            [
                'presc_id'             => 3,
                'record_id'            => 1, // Medical Record Dewi Kusuma ✓
                'product_id'           => 3, // Invisible SPF 50+ Sunscreen
                'product_name_snapshot'=> 'Invisible SPF 50+ Sunscreen',
                'qty'                  => 1,
                'dosage_instruction'   => 'Aplikasikan secukupnya sebagai tahap terakhir skincare pagi hari. Reapply setiap 2-3 jam jika beraktivitas di luar ruangan.',
                'prescribed_at'        => Carbon::parse('2025-05-05 10:10:00'),
                'created_at'           => Carbon::parse('2025-05-05 10:10:00'),
                'updated_at'           => Carbon::parse('2025-05-05 10:10:00'),
            ],

            // ── Resep untuk Record 2 (Anisa Putri) ────────────────────────
            // 3 item: Retinol Night Treatment, AHA BHA Toner, Centella Cream

            [
                'presc_id'             => 4,
                'record_id'            => 2, // Medical Record Anisa Putri ✓
                'product_id'           => 4, // Retinol Night Treatment
                'product_name_snapshot'=> 'Retinol Night Treatment',
                'qty'                  => 1,
                'dosage_instruction'   => 'Gunakan malam hari 3x seminggu (Senin, Rabu, Jumat). Mulai dengan lapisan tipis, tingkatkan frekuensi setelah 4 minggu jika tidak ada iritasi.',
                'prescribed_at'        => Carbon::parse('2025-05-07 14:55:00'),
                'created_at'           => Carbon::parse('2025-05-07 14:55:00'),
                'updated_at'           => Carbon::parse('2025-05-07 14:55:00'),
            ],
            [
                'presc_id'             => 5,
                'record_id'            => 2, // Medical Record Anisa Putri ✓
                'product_id'           => 7, // AHA BHA Exfoliating Toner
                'product_name_snapshot'=> 'AHA BHA Exfoliating Toner',
                'qty'                  => 1,
                'dosage_instruction'   => 'Gunakan kapas untuk mengaplikasikan toner pada wajah 2x seminggu malam hari. Jangan digunakan bersamaan dengan retinol dalam satu malam.',
                'prescribed_at'        => Carbon::parse('2025-05-07 14:55:00'),
                'created_at'           => Carbon::parse('2025-05-07 14:55:00'),
                'updated_at'           => Carbon::parse('2025-05-07 14:55:00'),
            ],
            [
                'presc_id'             => 6,
                'record_id'            => 2, // Medical Record Anisa Putri ✓
                'product_id'           => 8, // Calming Centella Cream
                'product_name_snapshot'=> 'Calming Centella Cream',
                'qty'                  => 2,
                'dosage_instruction'   => 'Aplikasikan tipis pada area yang kemerahan atau iritasi setelah laser setiap pagi dan malam. Gunakan sebagai moisturiser utama selama masa pemulihan pasca laser.',
                'prescribed_at'        => Carbon::parse('2025-05-07 14:55:00'),
                'created_at'           => Carbon::parse('2025-05-07 14:55:00'),
                'updated_at'           => Carbon::parse('2025-05-07 14:55:00'),
            ],
        ]);
    }
}