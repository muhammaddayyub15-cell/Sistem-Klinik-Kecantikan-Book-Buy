<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Model Product: Merepresentasikan tabel PRODUCTS di Product Service DB.
// Kolom penting:
//   - SKU: kode unik produk untuk identifikasi stok
//   - stock_qty: jumlah stok saat ini (diupdate via StockLog)
//   - price: harga satuan produk
// Relasi:
//   - BelongsTo ProductCategory (M:1)
//   - HasMany StockLog (1:N) — setiap perubahan stok dicatat di stock_logs
//
// CATATAN ARSITEKTUR:
// Tabel ini TIDAK memiliki relasi langsung ke Order & Payment Service DB.
// Saat order dibuat, data produk (nama, harga) di-snapshot ke tabel order_items
// di Order DB. Ini sesuai prinsip microservice — tidak ada cross-DB FK.
class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'product_name',
        'SKU',
        'category_id',
        'price',
        'stock_qty',
        'unit',
        // Tambahkan kolom lain sesuai migrasi
    ];

    // category: Relasi ke tabel product_categories.
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    // stockLogs: Relasi ke riwayat perubahan stok.
    // Digunakan untuk audit trail dan rekonstruksi stok jika diperlukan.
    public function stockLogs(): HasMany
    {
        return $this->hasMany(StockLog::class, 'product_id');
    }
}