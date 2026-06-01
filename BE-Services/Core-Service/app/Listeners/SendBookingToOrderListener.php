<?php

namespace App\Listeners;


// ALUR LENGKAP:
//   BookingController
//     → BookingService::store()
//       → event(new BookingCreated($booking))   ← fire event
//         → SendBookingToOrderListener::handle() ← ditangkap di sini
//           → SendBookingToOrderService::sendBookingToOrder()
//             → HTTP POST ke Order & Payment Service

//          Jika menggunakan auto-discovery, pastikan sudah run:
//          php artisan event:cache
// ============================================================

use App\Events\Booking\BookingCreated;
use App\Listeners\SendBookingToOrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

// STATUS: ✅
// CATATAN: Implement ShouldQueue jika ingin proses ini berjalan
//          secara ASYNC (direkomendasikan untuk production agar
//          response booking ke user tidak terhambat oleh HTTP call
//          ke Order Service yang mungkin lambat).
//
// Untuk aktifkan queue: hapus komentar "implements ShouldQueue"
// lalu jalankan: php artisan queue:work

class SendBookingToOrderListener // implements ShouldQueue
{
    // use InteractsWithQueue; // Aktifkan jika implements ShouldQueue

    /**
     * Jumlah percobaan ulang jika job gagal (hanya aktif jika ShouldQueue).
     *
     * STATUS: ⚠️ Hanya berlaku saat ShouldQueue diaktifkan
     */
    public int $tries = 3;

    /**
     * Delay antar retry dalam detik (hanya aktif jika ShouldQueue).
     *
     * STATUS: ⚠️ Hanya berlaku saat ShouldQueue diaktifkan
     */
    public int $backoff = 10;

    protected SendBookingToOrderService $sendBookingToOrderService;

    /**
     * Constructor - inject SendBookingToOrderService.
     *
     * STATUS: ✅
     * CATATAN: Laravel akan auto-resolve dependency ini
     *          karena sudah di-binding di AppServiceProvider.
     */
    public function __construct(SendBookingToOrderService $sendBookingToOrderService)
    {
        $this->sendBookingToOrderService = $sendBookingToOrderService;
    }

    /**
     * Handle the event.
     *
     * STATUS: ✅
     *
     * @param  BookingCreated $event  Event yang membawa instance Booking
     * @return void
     */
    public function handle(BookingCreated $event): void
    {
        $booking = $event->booking;

        Log::info('[SendBookingToOrderListener] Menerima event BookingCreated', [
            'booking_id' => $booking->booking_id,
        ]);

        // Panggil service untuk kirim ke Order & Payment Service
        $success = $this->sendBookingToOrderService->sendBookingToOrder($booking);

        if (!$success) {
            // CATATAN: Jika sync (bukan queue), gagal di sini tidak
            //          membatalkan booking. Booking tetap tersimpan di DB.
            // TODO: Pertimbangkan untuk menyimpan failed attempt ke tabel
            //       tersendiri agar bisa di-retry manual oleh admin.
            Log::warning('[SendBookingToOrderListener] Gagal mengirim booking ke Order Service', [
                'booking_id' => $booking->booking_id,
            ]);
        }
    }
}