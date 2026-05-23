<?php

namespace App\Service\Repositories;

use App\Models\Order\Order;
use App\Service\Shared\Base\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

// OrderRepository: Menangani semua query ke tabel orders.
class OrderRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    // findAllPaginated: Ambil semua order dengan pagination dan eager load relasi.
    public function findAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['orderItems', 'payment'])->paginate($perPage);
    }

    // findByPatientSnapshot: Ambil semua order milik pasien tertentu berdasarkan snapshot ID.
    // Menggunakan patient_id_snapshot karena tidak ada FK hidup ke Core Service.
    public function findByPatientSnapshot(int $patientId): Collection
    {
        return $this->model->with(['orderItems', 'payment'])
            ->where('patient_id_snapshot', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // findByOrderNumber: Cari order berdasarkan nomor order unik.
    // Digunakan untuk lookup dari sistem lain atau konfirmasi manual.
    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->model->with(['orderItems', 'payment'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    // findByStatus: Filter order berdasarkan status.
    // Status: pending, processing, completed, cancelled
    public function findByStatus(string $status): Collection
    {
        return $this->model->with(['orderItems', 'payment'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // updateStatus: Update status order berdasarkan ID.
    public function updateStatus(int $id, string $status, array $timestamps = []): Order
    {
        $order = $this->findById($id);
        $order->update(array_merge(['status' => $status], $timestamps));
        return $order->fresh();
    }
}