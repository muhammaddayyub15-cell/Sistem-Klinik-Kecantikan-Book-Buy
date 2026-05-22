<?php

namespace App\Service;


// ALUR:
//   1. BookingController membuat Booking baru
//   2. BookingService men-fire event BookingCreated
//   3. Listener (SendBookingToOrderListener) memanggil service ini
//   4. Service ini mengirim HTTP POST ke Order & Payment Service
//   5. Order & Payment Service membuat record di tabel ORDERS

// CATATAN PENTING:
//   - Tambahkan ORDER_SERVICE_URL di file .env
//   - Pastikan Order & Payment Service sudah berjalan
//   - Gunakan Queue/Job jika ingin proses ini async (direkomendasikan)

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Booking\Booking;
use App\Shared\Base\BaseService;

class SendBookingToOrderService extends BaseService
{
    /**
     * URL endpoint Order & Payment Service.
     *
     * STATUS: ⚠️ PERLU KONFIGURASI - Tambahkan di .env:
     *         ORDER_SERVICE_URL=http://order-service:8001
     *         ORDER_SERVICE_SECRET=your-secret-key
     *
     * CATATAN: Gunakan nama service Docker jika deploy dengan docker-compose,
     *          atau gunakan URL publik jika service terpisah server.
     */
    protected string $orderServiceUrl;

    /**
     * Secret key untuk autentikasi antar service.
     *
     * STATUS: ⚠️ PERLU KONFIGURASI
     * Ini adalah internal service-to-service auth key,
     * berbeda dengan JWT token user yang di-handle AuthController.
     */
    protected string $serviceSecret;

    public function __construct()
    {
        // Ambil URL dari environment variable
        // CATATAN: Jika env tidak di-set, default ke localhost:8001
        //          Sesuaikan dengan environment deployment Anda.
        $this->orderServiceUrl = env('ORDER_SERVICE_URL', 'http://localhost:8001');
        $this->serviceSecret   = env('ORDER_SERVICE_SECRET', '');
    }

    /**
     * Kirim data booking ke Order & Payment Service.
     *
     * STATUS: ✅ Method utama - dipanggil dari Listener
     *
     * @param  Booking $booking  Instance model Booking yang baru dibuat
     * @return bool              True jika berhasil, false jika gagal
     *
     * CATATAN: Method ini akan di-load dari event BookingCreated
     *          Pastikan model Booking sudah di-eager load relasi yang dibutuhkan:
     *          - booking->patient (untuk patient_id)
     *          - booking->service (untuk total_amount dari base_price)
     *          - booking->doctor (untuk info dokter)
     */
    public function sendBookingToOrder(Booking $booking): bool
    {
        try {
            // ── STEP 1: Siapkan payload data ─────────────────────────
            // STATUS: ✅
            // CATATAN: Sesuaikan field dengan yang dibutuhkan
            //          Order & Payment Service endpoint-nya.
            //          Lihat ERD tabel ORDERS untuk field yang diperlukan.

            $payload = $this->buildOrderPayload($booking);

            // ── STEP 2: Kirim HTTP POST ke Order Service ──────────────
            // STATUS: ✅
            // CATATAN: Menggunakan Laravel HTTP Client (wrapper Guzzle)
            //          Timeout di-set 30 detik untuk menghindari hanging request.

            $response = Http::withHeaders([
                    // Header autentikasi internal antar service
                    // CATATAN: Sesuaikan nama header dengan yang diexpect
                    //          oleh Order & Payment Service
                    'X-Service-Secret' => $this->serviceSecret,
                    'Accept'           => 'application/json',
                    'Content-Type'     => 'application/json',
                ])
                ->timeout(30) // ⚠️ Naikkan jika Order Service lambat respond
                ->post("{$this->orderServiceUrl}/api/orders", $payload);

            // ── STEP 3: Validasi response ─────────────────────────────
            // STATUS: ✅
            if ($response->successful()) {
                // Log sukses untuk debugging & audit trail
                Log::info('[SendBookingToOrderService] Booking berhasil dikirim ke Order Service', [
                    'booking_id' => $booking->booking_id,
                    'order_id'   => $response->json('data.order_id') ?? 'N/A',
                    'status'     => $response->status(),
                ]);

                return true;
            }

            // ── STEP 4: Handle response gagal (4xx/5xx) ───────────────
            // STATUS: ✅
            // CATATAN: Jika Order Service mengembalikan error,
            //          booking di Core Service tetap tersimpan,
            //          tapi order tidak terbuat. Perlu mekanisme retry
            //          atau notifikasi admin jika ini terjadi.

            Log::error('[SendBookingToOrderService] Order Service mengembalikan error', [
                'booking_id'    => $booking->booking_id,
                'http_status'   => $response->status(),
                'response_body' => $response->body(),
            ]);

            return false;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // ── HANDLE: Order Service tidak dapat dijangkau ───────────
            // STATUS: ✅
            // SKENARIO: Order Service down, network issue, URL salah
            // REKOMENDASI: Implementasikan Queue/Job agar bisa di-retry otomatis
            //              php artisan make:job SendBookingToOrderJob

            Log::critical('[SendBookingToOrderService] Tidak dapat terhubung ke Order Service', [
                'booking_id' => $booking->booking_id,
                'url'        => "{$this->orderServiceUrl}/api/orders",
                'error'      => $e->getMessage(),
            ]);

            // TODO: Pertimbangkan untuk throw exception agar Queue bisa retry
            // throw $e;

            return false;

        } catch (\Exception $e) {
            // ── HANDLE: Error tidak terduga ───────────────────────────
            // STATUS: ✅
            Log::error('[SendBookingToOrderService] Error tidak terduga', [
                'booking_id' => $booking->booking_id,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Build payload yang akan dikirim ke Order Service.
     *
     * STATUS: ✅
     * CATATAN: Sesuaikan struktur payload dengan API contract
     *          Order & Payment Service Anda.
     *          Field mengacu pada ERD tabel ORDERS:
     *          order_id, booking_id, patient_id, total_amount,
     *          order_number, status, payment_status
     *
     * @param  Booking $booking
     * @return array
     */
    protected function buildOrderPayload(Booking $booking): array
    {
        // CATATAN: Pastikan relasi berikut sudah di-load sebelum
        //          memanggil method ini (via eager loading di BookingService):
        //          $booking->load(['patient', 'service', 'doctor', 'doctorSchedule'])

        return [
            // Identifier booking dari Core Service
            // ERD ORDERS: booking_id (FK referencing BOOKINGS.booking_id)
            'booking_id' => $booking->booking_id,

            // Patient yang melakukan booking
            // ERD ORDERS: patient_id (FK referencing PATIENTS.patient_id)
            'patient_id' => $booking->patient_id,

            // Total amount dari base_price service
            // ERD ORDERS: total_amount
            // CATATAN: Jika ada diskon atau multiple service,
            //          kalkulasikan di sini sebelum dikirim
            'total_amount' => $booking->service->base_price ?? 0,

            // Generate order number unik
            // ERD ORDERS: order_number
            // FORMAT: ORD-{YYYYMMDD}-{booking_id}
            'order_number' => 'ORD-' . now()->format('Ymd') . '-' . $booking->booking_id,

            // Status awal order selalu 'pending'
            // ERD ORDERS: status (pending, completed, cancelled)
            'status' => 'pending',

            // Status awal pembayaran
            // ERD PAYMENTS: payment_status (ini akan di-update setelah payment)
            'payment_status' => 'unpaid',

            // Informasi tambahan untuk Order Service
            // CATATAN: Field ini opsional, sesuaikan dengan kebutuhan
            'meta' => [
                'doctor_id'    => $booking->doctor_id,
                'service_id'   => $booking->service_id,
                'booked_date'  => $booking->booked_date,
                'start_time'   => $booking->start_time,
                'end_time'     => $booking->end_time,
                'service_name' => $booking->service->service_name ?? null,
            ],
        ];
    }

    /**
     * Cek apakah Order Service dapat dijangkau (health check).
     *
     * STATUS: ✅ Opsional - berguna untuk debugging & monitoring
     * PENGGUNAAN: Bisa dipanggil dari artisan command atau health-check endpoint
     *             untuk memastikan koneksi antar service OK sebelum deploy.
     *
     * @return bool
     */
    public function pingOrderService(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->orderServiceUrl}/api/health");
            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('[SendBookingToOrderService] Order Service tidak merespons ping', [
                'url'   => "{$this->orderServiceUrl}/api/health",
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}