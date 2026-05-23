<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Model OrderItem: Merepresentasikan tabel ORDER_ITEMS di Order-Payment Service DB.
// Junction table antara ORDERS dan PRODUCTS (M:N).
//
// CATATAN ARSITEKTUR — SNAPSHOT PATTERN:
// Kolom snapshot yang disimpan dari Product Service:
//   - product_id_snapshot   : ID produk saat order dibuat
//   - product_name_snapshot : Nama produk saat order dibuat
//   - unit_price_snapshot   : Harga satuan saat order dibuat
//
// Alasan snapshot harga (unit_price_snapshot):
//   Harga produk bisa berubah kapan saja di Product Service.
//   Invoice harus mencatat harga saat transaksi, bukan harga hari ini.
//   Ini identik dengan prinsip struk belanja — harga di struk tidak berubah
//   meski harga produk naik keesokan harinya.
class OrderItem extends Model
{
    protected $table = 'order_items';

    // OrderItem tidak butuh softDeletes — item tidak bisa dihapus dari order yang sudah dibuat.
    // Untuk membatalkan item, batalkan seluruh order via status order.
    public $timestamps = true;

    protected $fillable = [
        'order_id',
        'product_id_snapshot',
        'product_name_snapshot',
        'unit_price_snapshot',
        'qty',
    ];

    protected $casts = [
        'unit_price_snapshot' => 'decimal:2',
    ];

    // order: Relasi balik ke tabel orders.
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}