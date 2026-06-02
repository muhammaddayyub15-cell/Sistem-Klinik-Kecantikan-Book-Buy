<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

// Model Order: Merepresentasikan tabel ORDERS di Order-Payment Service DB.
//
// CATATAN ARSITEKTUR — SNAPSHOT PATTERN:
// Tabel ini TIDAK memiliki FK hidup ke Core Service DB atau Product Service DB.
// Data dari service lain disimpan sebagai kolom snapshot (string/integer biasa).
//
// Kolom snapshot yang disimpan:
//   - patient_id_snapshot   : ID pasien saat order dibuat (dari Core Service)
//   - patient_name_snapshot : Nama pasien saat order dibuat (dari Core Service)
//   - booking_id_snapshot   : ID booking terkait (dari Core Service), nullable
//
// Alasan snapshot:
//   Data order tidak boleh berubah meski profil pasien di-update di Core Service.
//   Invoice harus mencatat kondisi data saat transaksi terjadi, bukan saat ini.
class Order extends Model
{
    use SoftDeletes;

    protected $table = 'orders';

    protected $primaryKey = 'order_id';

    protected $fillable = [
        'order_number',
        'patient_id_snapshot',
        'patient_name_snapshot',
        'booking_id_snapshot',
        'total_amount',
        'status',
        'paid_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'paid_at'      => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(\App\Models\Payment\Payment::class, 'order_id', 'order_id');
    }
}