<?php

namespace App\Service\Repositories;

use App\Models\Product\StockLog;
use App\Service\Shared\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

// StockLogRepository: Menangani query ke tabel stock_logs.
// Repository ini hanya mendukung operasi READ dan CREATE.
// DELETE tidak disediakan secara sengaja — log tidak boleh dihapus.
class StockLogRepository extends BaseRepository
{
    public function __construct(StockLog $model)
    {
        parent::__construct($model);
    }

    // createLog: Catat satu entri perubahan stok.
    // Dipanggil dari ProductService setiap kali stok berubah.
    public function createLog(array $data): StockLog
    {
        // Pastikan data yang masuk memiliki: product_id, change_qty, type
        return $this->model->create($data);
    }

    // findByProductId: Ambil riwayat stok untuk satu produk tertentu.
    // Diurutkan dari yang terbaru untuk kebutuhan audit.
    public function findByProductId(int $productId): Collection
    {
        return $this->model
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}