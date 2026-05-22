<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Model StockLog: Merepresentasikan tabel STOCK_LOGS di Product Service DB.
// Tujuan: audit trail setiap perubahan stok produk.
//
// Kolom yang direkomendasikan di migrasi:
//   - product_id (FK ke products)
//   - change_qty: jumlah perubahan (positif = stok masuk, negatif = stok keluar)
//   - type: enum ['in', 'out', 'adjustment'] — jenis perubahan stok
//   - reference_id: ID referensi opsional (misal order_id dari Order Service)
//   - notes: keterangan tambahan
//   - created_at, updated_at
//
// CATATAN: StockLog tidak menggunakan SoftDeletes karena merupakan audit log.
// Log tidak boleh dihapus untuk menjaga integritas riwayat stok.
class StockLog extends Model
{
    protected $table = 'stock_logs';

    protected $fillable = [
        'product_id',
        'change_qty',
        'type',
        'reference_id',
        'notes',
    ];

    // product: Relasi balik ke tabel products.
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}