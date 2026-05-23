<?php

namespace App\Service;

use App\Service\Repositories\OrderRepository;
use App\Service\Repositories\PaymentRepository;
use App\Service\Shared\Base\BaseService;
use Illuminate\Database\Eloquent\Model;

// PaymentService: Business logic untuk manajemen pembayaran via Midtrans.
//
// CATATAN ARSITEKTUR — ALUR PEMBAYARAN:
// 1. Client request POST /payments/initiate → PaymentService::initiate()
//    → Buat payment record (status: pending)
//    → Kirim request ke Midtrans API → dapat payment_url
//    → Kembalikan payment_url ke client untuk redirect
//
// 2. Midtrans kirim webhook POST /payments/webhook → PaymentService::handleWebhook()
//    → Verifikasi signature key dari Midtrans
//    → Update status payment berdasarkan transaction_status dari Midtrans
//    → Jika settlement → update order status ke completed via OrderService
//
// CATATAN: Integrasi Midtrans (HTTP call ke API Midtrans) akan ditambahkan
// setelah package midtrans-php terinstall. Saat ini method initiate() hanya
// menyiapkan struktur data yang diperlukan.
class PaymentService extends BaseService
{
    private PaymentRepository $paymentRepository;
    private OrderRepository $orderRepository;

    public function __construct(
        PaymentRepository $paymentRepository,
        OrderRepository $orderRepository
    ) {
        parent::__construct($paymentRepository);
        $this->paymentRepository = $paymentRepository;
        $this->orderRepository   = $orderRepository;
    }

    // getPaymentByOrderId: Ambil data payment berdasarkan order_id.
    public function getPaymentByOrderId(int $orderId): ?Model
    {
        return $this->paymentRepository->findByOrderId($orderId);
    }

    // initiate: Inisiasi pembayaran untuk satu order.
    // Membuat payment record baru dan mengembalikan data untuk proses ke Midtrans.
    // TODO: Tambahkan HTTP call ke Midtrans API setelah package midtrans-php terinstall.
    //       Response Midtrans berisi snap_token dan redirect_url yang dikembalikan ke client.
    public function initiate(int $orderId): Model
    {
        $order = $this->orderRepository->findById($orderId, ['orderItems']);

        // Validasi order harus berstatus pending sebelum bisa diproses pembayaran
        if ($order->status !== 'pending') {
            throw new \Exception('Order tidak dalam status pending, pembayaran tidak dapat diproses.', 422);
        }

        // Cek apakah payment record sudah ada untuk order ini
        $existingPayment = $this->paymentRepository->findByOrderId($orderId);
        if ($existingPayment) {
            throw new \Exception('Pembayaran untuk order ini sudah pernah diinisiasi.', 422);
        }

        // Buat payment record dengan status pending
        // midtrans_id akan diisi setelah mendapat response dari Midtrans API
        $payment = $this->paymentRepository->create([
            'order_id'        => $orderId,
            'midtrans_id'     => null, // diisi saat Midtrans API berhasil dipanggil
            'amount'          => $order->total_amount,
            'payment_methods' => null, // diisi setelah user memilih metode di halaman Midtrans
            'payment_channel' => null,
            'status'          => 'pending',
            'paid_at'         => null,
        ]);

        return $payment;
    }

    // handleWebhook: Proses notifikasi webhook dari Midtrans.
    // Dipanggil saat Midtrans mengirim POST ke /payments/webhook.
    // $payload: array data dari request body Midtrans webhook.
    //
    // Status Midtrans yang ditangani:
    //   - settlement → pembayaran berhasil → update order ke completed
    //   - pending    → menunggu pembayaran → tidak ada aksi
    //   - expire     → waktu pembayaran habis → update order ke cancelled
    //   - cancel     → dibatalkan → update order ke cancelled
    //   - deny       → ditolak → tidak ada aksi khusus
    public function handleWebhook(array $payload): void
    {
        // TODO: Verifikasi signature key Midtrans sebelum proses payload
        // $signatureKey = hash('sha512', $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . config('services.midtrans.server_key'));
        // if ($signatureKey !== $payload['signature_key']) {
        //     throw new \Exception('Signature Midtrans tidak valid.', 403);
        // }

        $midtransId        = $payload['transaction_id'];
        $transactionStatus = $payload['transaction_status'];

        // Cari payment berdasarkan midtrans_id
        $payment = $this->paymentRepository->findByMidtransId($midtransId);
        if (!$payment) {
            throw new \Exception('Data payment tidak ditemukan untuk transaksi ini.', 404);
        }

        match ($transactionStatus) {
            // Pembayaran berhasil dikonfirmasi Midtrans
            'settlement' => $this->handleSettlement($payment),
            // Waktu pembayaran habis atau user membatalkan
            'expire', 'cancel' => $this->handleCancellation($payment),
            // Status lain (pending, deny) — tidak ada aksi khusus
            default => null,
        };
    }

    // handleSettlement: Proses saat pembayaran settlement (berhasil).
    private function handleSettlement(Model $payment): void
    {
        // Update status payment ke settlement
        $this->paymentRepository->updateStatus($payment->id, 'settlement', now()->toDateTimeString());

        // Update status order ke completed
        $this->orderRepository->updateStatus($payment->order_id, 'completed', [
            'paid_at'       => now(),
            'completed_at'  => now(),
        ]);
    }

    // handleCancellation: Proses saat pembayaran expire atau cancel.
    private function handleCancellation(Model $payment): void
    {
        // Update status payment
        $this->paymentRepository->updateStatus($payment->id, 'cancel');

        // Update status order ke cancelled
        $this->orderRepository->updateStatus($payment->order_id, 'cancelled', [
            'cancelled_at' => now(),
        ]);
    }
}