<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Model Payment: Merepresentasikan tabel PAYMENTS di Order-Payment Service DB.
// Relasi 1:1 dengan ORDERS — satu order hanya memiliki satu transaksi pembayaran.
//
// Kolom Midtrans yang disimpan:
//   - midtrans_id       : transaction_id dari Midtrans (untuk lookup di dashboard Midtrans)
//   - payment_methods   : metode pembayaran (gopay, bca_va, bni_va, dll)
//   - payment_channel   : channel pembayaran (qris, bank_transfer, dll)
//   - status            : status transaksi Midtrans (pending, settlement, expire, cancel, deny)
//   - paid_at           : timestamp saat Midtrans mengirim notifikasi settlement
//
// CATATAN: Jangan hapus record payment meski order dibatalkan.
// Payment record adalah bukti transaksi — hanya status yang diupdate.
class Payment extends Model
{
    protected $table = 'payments';

    public $timestamps = true;

    protected $fillable = [
        'order_id',
        'midtrans_id',
        'amount',
        'payment_methods',
        'payment_channel',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // order: Relasi balik ke tabel orders.
    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Order\Order::class, 'order_id');
    }
}