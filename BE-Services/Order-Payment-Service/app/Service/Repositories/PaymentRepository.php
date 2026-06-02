<?php

namespace App\Service\Repositories;

use App\Models\Payment\Payment;
use App\Service\Shared\Base\BaseRepository;

// PaymentRepository: Query layer untuk tabel payments.
// Relasi 1:1 dengan orders — satu order hanya punya satu payment record.
class PaymentRepository extends BaseRepository
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    // findByOrderId: Ambil payment berdasarkan order_id.
    // Dipakai saat cek status pembayaran dan saat webhook masuk.
    public function findByOrderId(int $orderId): ?Payment
    {
        return $this->model->where('order_id', $orderId)->first();
    }

    // findByMidtransId: Cari payment berdasarkan transaction_id Midtrans.
    // Dipakai jika perlu lookup manual via dashboard Midtrans.
    public function findByMidtransId(string $midtransId): ?Payment
    {
        return $this->model->where('midtrans_id', $midtransId)->first();
    }

    // updateStatus: Update status payment dan opsional timestamp paid_at.
    // $id      : payment_id (primary key)
    // $status  : 'pending' | 'paid' | 'cancel' | 'expired'
    // $paidAt  : timestamp string, diisi hanya saat status 'paid'
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