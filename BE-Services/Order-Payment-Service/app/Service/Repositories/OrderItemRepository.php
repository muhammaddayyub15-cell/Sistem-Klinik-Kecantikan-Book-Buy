<?php

namespace App\Service\Repositories;

use App\Models\Order\OrderItem;
use App\Service\Shared\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

// OrderItemRepository: Menangani query ke tabel order_items.
// Repository ini hanya mendukung CREATE dan READ.
// Item tidak bisa diupdate atau dihapus setelah order dibuat —
// untuk membatalkan, gunakan updateStatus di OrderRepository.
class OrderItemRepository extends BaseRepository
{
    public function __construct(OrderItem $model)
    {
        parent::__construct($model);
    }

    // createBulk: Simpan banyak item sekaligus dalam satu order.
    // Dipanggil dari OrderService saat order baru dibuat.
    // $items: array of ['order_id', 'product_id_snapshot', 'product_name_snapshot', 'unit_price_snapshot', 'qty']
    public function createBulk(array $items): bool
    {
        return $this->model->insert($items);
    }

    // findByOrderId: Ambil semua item dalam satu order.
    public function findByOrderId(int $orderId): Collection
    {
        return $this->model->where('order_id', $orderId)->get();
    }
}