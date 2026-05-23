<?php

namespace App\Service\Repositories;

use App\Models\Payment\Payment;
use App\Service\Shared\Base\BaseRepository;

// PaymentRepository: Menangani query ke tabel payments.
// Relasi 1:1 dengan orders — satu order hanya punya satu payment record.
class PaymentRepository extends BaseRepository
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    // findByOrderId: Ambil data payment berdasarkan order_id.
    // Digunakan saat cek status pembayaran suatu order.
    public function findByOrderId(int $orderId): ?Payment
    {
        return $this->model->where('order_id', $orderId)->first();
    }

    // findByMidtransId: Cari payment berdasarkan transaction_id dari Midtrans.
    // Digunakan saat menerima webhook notifikasi dari Midtrans untuk mencocokkan transaksi.
    public function findByMidtransId(string $midtransId): ?Payment
    {
        return $this->model->where('midtrans_id', $midtransId)->first();
    }

    // updateStatus: Update status payment dan timestamp paid_at.
    // Dipanggil dari PaymentService saat webhook Midtrans diterima.
    public function updateStatus(int $id, string $status, ?string $paidAt = null): Payment
    {
        $payment = $this->findById($id);
        $payment->update([
            'status'  => $status,
            'paid_at' => $paidAt,
        ]);
        return $payment->fresh();
    }
}