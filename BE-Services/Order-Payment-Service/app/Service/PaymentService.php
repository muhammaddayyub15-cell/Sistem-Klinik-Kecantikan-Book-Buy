<?php

namespace App\Service;

use App\Models\Order\Order;
use App\Service\Repositories\OrderRepository;
use App\Service\Repositories\PaymentRepository;
use App\Service\Shared\Base\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

// PaymentService: Business logic untuk manajemen pembayaran via Midtrans.
//
// ALUR PEMBAYARAN:
// 1. Client → POST /payments/initiate → initiate()
//    → Validasi order status pending
//    → Generate order_number unik
//    → Buat payment record (status: pending)
//    → Kirim Snap request ke Midtrans → dapat snap_token + snap_url
//    → Kembalikan snap_token + snap_url ke client
//
// 2. Midtrans → POST /payments/webhook → handleWebhook()
//    → Verifikasi signature key
//    → Cari payment via order_number di payload
//    → Update status payment + order sesuai transaction_status
class PaymentService extends BaseService
{
    public function __construct(
        private PaymentRepository $paymentRepository,
        private OrderRepository $orderRepository
    ) {
        parent::__construct($paymentRepository);
        $this->configureMidtrans();
    }

    // -------------------------------------------------------------------------
    // Public
    // -------------------------------------------------------------------------

    // getPaymentByOrderId: Ambil data payment berdasarkan order_id.
    public function getPaymentByOrderId(int $orderId): ?Model
    {
        return $this->paymentRepository->findByOrderId($orderId);
    }

    // initiate: Inisiasi pembayaran — buat payment record + minta Snap token ke Midtrans.
    // Return array berisi snap_token dan snap_url untuk diteruskan ke client.
    public function initiate(int $orderId): array
    {
        $order = $this->orderRepository->findById($orderId, ['orderItems']);

        if ($order->status !== 'pending') {
            throw new \Exception('Order tidak dalam status pending, pembayaran tidak dapat diproses.', 422);
        }

        $existingPayment = $this->paymentRepository->findByOrderId($orderId);
        if ($existingPayment) {
            throw new \Exception('Pembayaran untuk order ini sudah pernah diinisiasi.', 422);
        }

        // Generate order_number unik — format: ORD-<timestamp>-<6 char random>
        // Dipakai sebagai identifier di Midtrans (harus string & unik per transaksi)
        $orderNumber = 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(6));

        // Simpan order_number ke tabel orders agar bisa di-lookup saat webhook masuk
        $this->orderRepository->updateOrderNumber($order->order_id, $orderNumber);

        // Buat payment record dengan status pending sebelum call Midtrans
        // Jika Midtrans gagal, record ini tetap ada sebagai log — tidak masalah
        $payment = $this->paymentRepository->create([
            'order_id'        => $orderId,
            'midtrans_id'     => null,
            'amount'          => $order->total_amount,
            'payment_methods' => null,
            'payment_channel' => null,
            'status'          => 'pending',
            'paid_at'         => null,
        ]);

        $snapToken = Snap::getSnapToken($this->buildSnapParams($order, $orderNumber));

        return [
            'snap_token'   => $snapToken,
            'snap_url'     => $this->snapUrl($snapToken),
            'order_number' => $orderNumber,
            'amount'       => $order->total_amount,
        ];
    }

    // handleWebhook: Proses notifikasi dari Midtrans.
    // Midtrans mengirim order_number (bukan order_id integer) di field 'order_id' payload.
    public function handleWebhook(array $payload): void
    {
        $this->verifySignature($payload);

        $orderNumber       = $payload['order_id'];          // ini adalah order_number kita
        $transactionStatus = $payload['transaction_status'];
        $fraudStatus       = $payload['fraud_status'] ?? null;

        // Cari order via order_number — field yang kita kirim ke Midtrans saat initiate
        $order = $this->orderRepository->findByOrderNumber($orderNumber);
        if (!$order) {
            throw new \Exception("Order dengan order_number {$orderNumber} tidak ditemukan.", 404);
        }

        $payment = $this->paymentRepository->findByOrderId($order->order_id);
        if (!$payment) {
            throw new \Exception("Payment untuk order_number {$orderNumber} tidak ditemukan.", 404);
        }

        // Update midtrans_id sekarang sudah ada di payload
        $payment->update([
            'midtrans_id'     => $payload['transaction_id'] ?? null,
            'payment_methods' => $payload['payment_type'] ?? null,
            'payment_channel' => $payload['bank'] ?? $payload['acquirer'] ?? null,
        ]);

        match (true) {
            // capture (kartu kredit) + tidak fraud = lunas
            $transactionStatus === 'capture' && $fraudStatus === 'accept',
            // transfer bank / e-wallet
            $transactionStatus === 'settlement'
                => $this->handleSettlement($payment),

            in_array($transactionStatus, ['expire', 'cancel'])
                => $this->handleCancellation($payment),

            // pending, deny — tidak ada aksi, tunggu webhook berikutnya
            default => null,
        };
    }

    // -------------------------------------------------------------------------
    // Private
    // -------------------------------------------------------------------------

    private function configureMidtrans(): void
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    private function buildSnapParams(Order $order, string $orderNumber): array
    {
        return [
            'transaction_details' => [
                'order_id'     => $orderNumber,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->patient_name_snapshot,
            ],
            'item_details' => $order->orderItems->map(fn ($item) => [
                'id'       => (string) $item->product_id_snapshot,
                'price'    => (int) $item->unit_price_snapshot,
                'quantity' => (int) $item->qty,
                'name'     => mb_substr($item->product_name_snapshot, 0, 50),
            ])->toArray(),
        ];
    }

    // verifySignature: Pastikan webhook benar-benar dari Midtrans.
    // Formula: SHA512(order_id + status_code + gross_amount + server_key)
    private function verifySignature(array $payload): void
    {
        $expected = hash('sha512',
            ($payload['order_id']     ?? '') .
            ($payload['status_code']  ?? '') .
            ($payload['gross_amount'] ?? '') .
            config('midtrans.server_key')
        );

        if (($payload['signature_key'] ?? '') !== $expected) {
            throw new \Exception('Signature Midtrans tidak valid.', 403);
        }
    }

    private function handleSettlement(Model $payment): void
    {
        $this->paymentRepository->updateStatus(
            $payment->payment_id,
            'paid',
            now()->toDateTimeString()
        );

        $this->orderRepository->updateStatus($payment->order_id, 'completed', [
            'paid_at'      => now(),
            'completed_at' => now(),
        ]);
    }

    private function handleCancellation(Model $payment): void
    {
        $this->paymentRepository->updateStatus($payment->payment_id, 'cancel');

        $this->orderRepository->updateStatus($payment->order_id, 'cancelled', [
            'cancelled_at' => now(),
        ]);
    }

    private function snapUrl(string $token): string
    {
        $base = config('midtrans.is_production')
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';

        return "{$base}/snap/v2/vtweb/{$token}";
    }
}